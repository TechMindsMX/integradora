<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

    function safeform() {
        $document = JFactory::getDocument();
        $this->app 	    = JFactory::getApplication();
        $data           = $this->app->input->getArray();
        $odv            = array();
        $validacion     = new validador();
        $envio['url']   = null;
        $diccionario    = array('account'       => array('tipo'=>'number', 'length' => ''),
                                'clientId'      => array('tipo'=>'number', 'length' => ''),
                                'conditions'    => array('tipo'=>'number', 'length' => ''),
                                'paymentMethod' => array('tipo'=>'number', 'length' => ''),
                                'placeIssue'    => array('tipo'=>'number', 'length' => ''),
                                'projectId'     => array('tipo'=>'number', 'length' => ''),
                                'projectId2'    => array('tipo'=>'number', 'length' => ''));

        $document->setMimeEncoding('application/json');
        JResponse::setHeader('Content-Disposition','attachment; filename="result.json"');

        foreach($diccionario as $key => $value){
            $envio[$key] = $data[$key];
        }

        foreach ($data as $key => $value) {
            if(!is_bool(strpos($key, 'cantidad'))){
                $cantidades[] = $value;
                $diccionario[$key] = array('tipo' => 'number', 'length' => '6');
            }
            if(!is_bool(strpos($key, 'productos'))){
                $productos[] = $value;
                $diccionario[$key] = array('tipo' => 'number', 'length' => '6');
            }
            if(!is_bool(strpos($key, 'descripcion'))){
                $descriptions[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '2500');
            }
            if(!is_bool(strpos($key, 'p_unitario'))){
                $punitario[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '50');
            }
            if(!is_bool(strpos($key, 'unidad'))){
                $unidades[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }
            if(!is_bool(strpos($key, 'iva'))){
                $iva[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }
            if(!is_bool(strpos($key, 'ieps'))){
                $ieps[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }

        }

        $resultValidacion  = $validacion->procesamiento($data, $diccionario);

        foreach($resultValidacion as $value){
            if(!is_bool($value)){
                echo json_encode($resultValidacion);
                return;
            }
        }

        for($i = 0; $i < count($cantidades); $i++){
            $obj = new stdClass();

            $obj->productos     = $productos[$i];
            $obj->cantidades    = $cantidades[$i];
            $obj->descripcion   = $descriptions[$i];
            $obj->unidades      = $unidades[$i];
            $obj->pUnitario     = $punitario[$i];
            $obj->iva           = $iva[$i];
            $obj->ieps          = $ieps[$i];

            $odv[]= $obj;
        }
        if($data['tab'] == 'ordenVenta'){
            $envio['redirect'] = true;
            $envio['url'] = JRoute::_('index.php?option=com_mandatos&view=odvpreview&integradoId=1&odvnum=1');
        }else{
            $envio['redirect'] = false;
        }
        $envio['odv'] = json_encode($odv);

        echo json_encode($envio);
    }
}
