<?php
/**
 * @version     1.0.1
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Lutek <luis.magana@techminds.com.mx>
 */

// No direct access.
use Integralib\IntFactory;

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

class AdminintegradoraControllertxsform extends JControllerAdmin{
    public $id_tx_banco;
    protected $data;

    public function save(){
        $post = array(
            'idtx'               => 'INT',
            'integradoId'        => 'STRING',
            'integradoIdPagador' => 'STRING',
            'monto'              => 'FLOAT',
            'cuenta'             => 'INT'
        );
    	$data     = JFactory::getApplication()->input->getArray($post);
        $transfer = new transferFunds( '', IntFactory::getIntegradoSimple($data['integradoIdPagador']), IntFactory::getIntegradoSimple($data['integradoId']), $data['monto'] );
        $result   = $transfer->sendCreateTx(false);

        if($result){
            $db = JFactory::getDbo();
            $db->transactionStart();

            try{
                $txbanco = new stdClass();
                $txbanco->id = $data['idtx'];
                $txbanco->identified = 1;

                $db->updateObject('#__txs_banco_integrado', $txbanco, 'id');

                $txsTimone = new stdClass();
                $txsTimone->idtx = $transfer->getTransferData();
                $txsTimone->integradoId = $data['integradoId'];
                $txsTimone->date = time();

                $db->insertObject('#__txs_timone_mandato',$txsTimone);

                $txrelation = new stdClass();
                $txrelation->id_txs_banco = $txbanco->id;
                $txrelation->id_txs_timone = $db->insertid();

                $db->insertObject('#__txs_banco_timone_relation', $txrelation);

                $data['txTimoneUuid'] = $txsTimone->idtx;
                $this->sendNotification($data);

                $db->transactionCommit();


                $resultado = true;
            }catch (Exception $e){
                $db->transactionRollback();
                $transfer = new transferFunds( '', $data['integradoId'], $data['integradoIdPagador'], $data['monto'] );
                $transfer->sendCreateTx(false);
                $resultado = false;
            }

            if(!$resultado){
                JFactory::getApplication()->enqueueMessage(JText::_('SINIDENTIFICAR_FORM_ERROR'), 'error');
                JFactory::getApplication()->redirect('index.php?option=com_adminintegradora&view=txslist');
            }else{
                JFactory::getApplication()->enqueueMessage(JText::_('SINIDENTIFICAR_FORM_OK'), 'message');
                JFactory::getApplication()->redirect('index.php?option=com_adminintegradora&view=txslist');
            }

        }
    }

    private function sendNotification($data){
        $getCurrUser = new IntegradoSimple($data['integradoId']);
        $integradora = new \Integralib\Integrado();
        $integradora->getIntegradora();

        foreach ($integradora->integrado->integrados[0]->datos_bancarios as $dataBank) {
            if($dataBank->datosBan_id == $data['cuenta']){
                $datosBanco = $dataBank;
            }
        }

        foreach ($getCurrUser->integrados[0]->datos_bancarios as $key => $banco) {
            if($banco->bankName == 'STP'){
                $integradoBanco = $banco;
            }
        }


        $dataMail = array(
            $getCurrUser->getDisplayName(),
            '$'.number_format($data['monto'], 2),
            date('d-m-Y',time()),
            $datosBanco->bankName, //TODO: cuales son los datos del banco (emisor y receptor)
            $integradoBanco->bankName,
            $data['txTimoneUuid'],
        );
        $send = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);

        $resultado = $send->sendNotifications('20',$dataMail);

        return $resultado;

    }
}