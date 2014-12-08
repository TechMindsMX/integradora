<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllersolicitudliquidacion extends JControllerAdmin {

    function saveform() {
        $document       = JFactory::getDocument();
        $this->app 	    = JFactory::getApplication();
        $parametros     = array(
            'saldo'       => 'FLOAT',
            'integradoId' => 'INT',
            'monto'       => 'FLOAT'
        );
        $data           = $this->app->input->getArray($parametros);
        $validacion     = new validador();
        $diccionario    = array('integradoId'   => array('tipo'=>'number', 'length' => '1'),
                                'monto'         => array('tipo'=>'float', 'length' => '15'),
                                'saldo'         => array('tipo'=>'float', 'length' => '15'));
        $valida = $validacion->procesamiento($data, $diccionario);
        $document->setMimeEncoding('application/json');

        foreach ($valida as $key => $value) {
            if(!is_bool($value)){
                echo json_encode($valida);
                return;
            }
        }
        $save = new sendToTimOne();
        $save->sendSolicitudLiquidacionTIMONE($data['monto'], $data['integradoId']);

        $sesion = JFactory::getSession();
        $nuevoSaldo = $data['saldo'] - $data['monto'];
        $idTX = $sesion->get('idTx', 0,'solicitudliquidacion');

        $sesion->set('idTx',$idTX+1, 'solicitudliquidacion');
        $sesion->set('nuevoSaldo',$nuevoSaldo, 'solicitudliquidacion');

        $respuesta = array();
        $respuesta['nuevoSaldo']     = (FLOAT) $nuevoSaldo;
        $respuesta['nuevoSaldoText'] = number_format($nuevoSaldo,2);
        $respuesta['idTx']           = (INT) $idTX;
        $respuesta['success']        = true;

        echo json_encode($respuesta);
    }
}
