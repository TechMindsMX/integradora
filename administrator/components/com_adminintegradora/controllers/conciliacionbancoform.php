<?php
/**
 * @version     1.0.1
 * @package     com_donde_comprar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      ismael <aguilar_2001@hotmail.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

class AdminintegradoraControllerConciliacionBancoForm extends JControllerAdmin{
    public $id_tx_banco;
    protected $data;
    private $receptor;

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
                $algo = $db->insertObject('#__txs_banco_integrado', $dataObj);
            } else {
                $algo = $db->updateObject('#__txs_banco_integrado', $dataObj, 'id');
            }

            $id_tx_banco = $db->insertid();
            $txTimone    = $this->makeTxTimone();//pasa el Saldo a Integradora antens de enviarlo al usuario
//            $this->saveTxsRelation( $txTimone->data, $id_tx_banco );

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
        $integradora = new \Integralib\Integrado();
        $integrados = Integrado::getAllIds();
        if (!array_key_exists($this->data['integradoId'], $integrados)) {
            // Si el id de integrado no es correcto, se asocia la TX con la Integradora
            $this->data['integradoId'] = $integradora->getIntegradoraUuid();
        }
    }

    private function makeTxTimone() {
        $integradora = new \Integralib\Integrado();

        $emisor = new IntegradoSimple($integradora->getIntegradoraUuid());
        $emisor->getTimOneData();

        $receptor = new IntegradoSimple($this->data['integradoId']);
        $receptor->getTimOneData();

        $send = new \Integralib\TimOneRequest();
        $result = $send->sendCashInTx($emisor->timoneData->timoneUuid, $receptor->timoneData->timoneUuid, $this->data['amount'], $this->data['referencia']);

        if ($result->code != 200) {
            throw new Exception('Error en '.__METHOD__.' = '.$result->code);
        }

        return $result;
    }

    private function saveTxsRelation($txTimone, $id_tx_banco) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $values = array($db->quote($txTimone), $db->quote(time()), $db->quote($this->data['integradoId']));

        $query->insert($db->quoteName('#__txs_timone_mandato'));
        $query->columns($db->quoteName(array('idTx', 'date', 'integradoId')));
        $query->values(implode(',',$values));

        $db->setQuery($query);
        $db->execute();
        $id_tx_timone = $db->insertid();

        $query = $db->getQuery(true);

        $values = array($db->quote($id_tx_banco), $db->quote($id_tx_timone));

        $query->insert($db->quoteName('#__txs_banco_timone_relation'));
        $query->columns($db->quoteName(array('id_txs_banco', 'id_txs_timone')));
        $query->values(implode(',',$values));

        $db->setQuery($query);
        $db->execute();
    }

    public function makeTransferIntegradoraIntegrado( $dataObj ) {
        $integradora = new \Integralib\Integrado();
        $transfer = new transferFunds( '', $integradora->getIntegradoraUuid(), $dataObj->integradoId, $dataObj->amount );
        $result = $transfer->sendCreateTx(false);

        $this->saveTxsRelation( $transfer->getTransferData(), $this->id_tx_banco );

        if($result != 200) {
            throw new Exception('Fallo al hacer la Tx Integradora Integrado');
        }
    }

    private function sendNotification(){
        $getCurrUser = new IntegradoSimple($this->data['integradoId']);
        $integradora = new \Integralib\Integrado();
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