<?php
/**
 * @version     1.0.1
 * @package     com_donde_comprar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      ismael <aguilar_2001@hotmail.com> - http://
 */

// No direct access.
use Integralib\Integrado;
use Integralib\IntFactory;
use Integralib\RelacionaTx;

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

class AdminintegradoraControllerConciliacionBancoForm extends JControllerAdmin{
    public $id_tx_banco;
    protected $data;

    public function save(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $post = array(
            'id'            => 'INT',
            'integradoId'   => 'STRING',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );

        $this->data = JFactory::getApplication()->input->getArray($post);
        $changeDateType = new DateTime($this->data['date']);

        $this->data['date'] = $changeDateType->getTimestamp();

        $this->verifyIntegrado();

        $dataObj = (object)$this->data;

        $db->transactionStart();

        try {
            if ( is_null( $this->data['id'] ) ) {
                unset($dataObj->id);
                $db->insertObject('#__txs_banco_integrado', $dataObj);
            } else {
                $db->updateObject('#__txs_banco_integrado', $dataObj, 'id');
            }

            $id_tx_banco = $db->insertid();
            $txTimone    = $this->makeTxTimone();//pasa el Saldo a Integradora antens de enviarlo al usuario

            if ( $txTimone->code == 200 ) {
                $this->id_tx_banco = $id_tx_banco;
                $this->makeTransferIntegradoraIntegrado( $dataObj );
            }

            $db->transactionCommit();

            $this->sendNotification();
            $app->enqueueMessage( JText::_( 'LBL_SAVED' ), 'MESSAGE' );

        } catch (Exception $e) {
            $db->transactionRollback();

            JLog::add($e->getMessage(), JLog::ERROR);

            $app->enqueueMessage( JText::_( 'LBL_NO_SAVED' ), 'WARNING' );
        }
        $app->redirect('index.php?option=com_adminintegradora&view=conciliacionbancoform');
    }

    private function verifyIntegrado() {
        $integradoConcentradora = new IntegradoSimple(INTEGRADOID_CONCENTRADORA);
        $integrados  = \Integrado::getAllIds();

        if (!array_key_exists($this->data['integradoId'], $integrados)) {
            // Si el id de integrado no es correcto, se asocia la TX con la Integradora
            $this->data['integradoId'] = $integradoConcentradora->getId();
            $this->data['identified'] = 0;
        }else{
            $this->data['identified'] = 1;
        }
    }

    private function makeTxTimone() {
        $integradora = new Integrado();

        $emisor = new IntegradoSimple($integradora->getIntegradoraUuid());
        $emisor->getTimOneData();

        $send = new \Integralib\TimOneRequest();
        $result = $send->sendCashInTx($emisor->timoneData->timoneUuid, $this->data['amount']);

        if ($result->code != 200) {
            throw new Exception('Error en '.__METHOD__.' = '.$result->code);
        }

        return $result;
    }

    public function makeTransferIntegradoraIntegrado( $dataObj ) {
        $integradora = new Integrado();
        $integradora = IntFactory::getIntegradoSimple( $integradora->getIntegradoraUuid() );

        $integrado = IntFactory::getIntegradoSimple( $dataObj->integradoId );

        $transfer = new transferFunds( '', $integradora, $integrado, $dataObj->amount );
        $result   = $transfer->sendCreateTx(false);

        if( $dataObj->integradoId !=  INTEGRADOID_CONCENTRADORA ) {
            $asociacion = new RelacionaTx();
            $asociacion->asociacionTxs($transfer->getTransferData(), $this->id_tx_banco, $this->data['integradoId']);
        }

        if($result != 200) {
            throw new Exception('Fallo al hacer la Tx Integradora Integrado');
        }
    }

    private function sendNotification(){
        $getCurrUser = new IntegradoSimple($this->data['integradoId']);
        $integradora = new Integrado();
        $integradora->getIntegradora();

        foreach ($integradora->integrado->integrados[0]->datos_bancarios as $dataBank) {
            if($dataBank->datosBan_id == $this->data['cuenta']){
                $datosBanco = $dataBank;
            }
        }

        $data = array(
            $getCurrUser->getDisplayName(),
            '$'.number_format($this->data['amount'], 2),
            date('d-m-Y',$this->data['date']),
            $datosBanco->bankName,
            $datosBanco->bankName, //TODO: cuales son los datos del banco (emisor y receptor)
            $this->data['referencia'],
        );

        $send = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);
        $resultado = $send->sendNotifications('20',$data);

        return $resultado;

    }

}