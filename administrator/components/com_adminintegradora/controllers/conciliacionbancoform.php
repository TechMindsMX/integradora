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

class AdminintegradoraControllerConciliacionBancoForm extends JControllerAdmin{
    protected $data;
    private $receptor;

    public function confirmacion(){
        JFactory::getDocument()->setMimeEncoding('application/json');
        $post = array(
            'id'            => 'INT',
            'confirmacion'  => 'INT',
            'integradoId'   => 'INT',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $validaciones = new validador();
        $data = JFactory::getApplication()->input->getArray($post);

        $diccionario = array(
            'integradoId' => array('number'  => true, 'maxlength' => 10),
            'cuenta'      => array('number'  => true, 'maxlength' => 3),
            'referencia'  => array('string'  => true, 'maxlength' => 21),
            'date'        => array('fecha'   => true, 'maxlength' => 10),
            'amount'      => array('float'   => true, 'maxlength' => 20)
        );
        $resultadovalidacion = $validaciones->procesamiento($data,$diccionario);
        var_dump($data,$resultadovalidacion);exit;

    }

    public function save(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $post = array(
            'id'            => 'INT',
            'integradoId'   => 'INT',
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

            $txTimone    = $this->makeTxTimone();
            $id_tx_banco = $db->insertid();
            $this->saveTxsRelation( $txTimone->data, $id_tx_banco );

            if ( is_numeric($txTimone) ) {
                $this->makeTransferIntegradoraIntegrado( $dataObj );
            }

            $db->transactionCommit();
            $app->enqueueMessage( JText::_( 'LBL_SAVED' ), 'MESSAGE' );

        } catch (Exception $e) {
            $db->transactionRollback();

            JLog::add($e->getMessage(), JLog::ERROR);

            $app->enqueueMessage( JText::_( 'LBL_NO_SAVED' ), 'WARNING' );
        }
        $app->redirect('index.php?option=com_adminintegradora&view=conciliacionbancoform');
    }

    private function verifyIntegrado() {
        $integrados = Integrado::getAllIds();
        if (!array_key_exists($this->data['integradoId'], $integrados)) {
            // Si el id de integrado no es correcto, se asocia la TX con la Integradora
            $this->data['integradoId'] = 1;
        }
    }

    private function makeTxTimone() {
        $this->receptor = new IntegradoSimple(1);
        $this->receptor->getTimOneData();

        $send = new \Integralib\TimOneRequest();
        $result = $send->sendCashInTx($this->receptor->timoneData->timoneUuid, $this->data['amount']);

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
        $query->columns($db->quoteName(array('idTx', 'date', 'idIntegrado')));
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

    /**
     * @param $dataObj
     */
    public function makeTransferIntegradoraIntegrado( $dataObj ) {
        $transfer = new transferFunds( '', 1, $dataObj->integradoId, $dataObj->amount );
        $result = $transfer->sendCreateTx();

        if($result->code != 200) {
            throw new Exception('Fallo al hacer la Tx Integradora Integrado');
        }
    }

}