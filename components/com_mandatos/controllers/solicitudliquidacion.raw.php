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
        $data           = $this->app->input->getArray();
        $validacion     = new validador();
        $diccionario    = array('integradoId'   => array('tipo'=>'number', 'length' => '1'),
                                'monto'         => array('tipo'=>'float', 'length' => '15'),
                                'saldo'         => array('tipo'=>'float', 'length' => '15'));
        $valida = $validacion->procesamiento($data, $diccionario);
        $document->setMimeEncoding('application/json');
        JResponse::setHeader('Content-Disposition','attachment; filename="result.json"');

        foreach ($valida as $key => $value) {
            if(!is_bool($value)){
                echo json_encode($valida);
                return;
            }
        }
        exit;
    }
}
