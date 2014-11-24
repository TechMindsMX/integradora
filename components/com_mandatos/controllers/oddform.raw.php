<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOddform extends JControllerLegacy {

    function saveODD() {
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray();
        $datos = json_decode($this->parametros['datos']);
        $save = new sendToTimOne();
        $date = new DateTime($datos->paymentDate);
        $datos->paymentDate = $date->getTimestamp();

        unset($datos->view, $datos->option, $datos->Itemid, $datos->confirmacion);

        $datos->createdDate = time();

        $datos->numOrden = $save->getNextOrderNumber('odd', $datos->integradoId);

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $datos->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }

        $save->formatData($datos);
        $salvado = $save->insertDB('ordenes_deposito');

        if($salvado) {
            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&view=oddpreview&integradoId=' . $datos->integradoId . '&oddnum=' . $datos->numOrden.'&success=true',
                'redireccion' => true);
        }else{
            $respuesta = array('redireccion' => false);
        }

        JFactory::getDocument()->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    function valida(){
        $validacion = new validador();
        $document = JFactory::getDocument();
        $parametros = JFactory::getApplication()->input->getArray();
        $diccionario = array('paymentMethod' => array('tipo'=>'number', 'length' => 10),
            'paymentDate' => array('tipo'=>'fecha', 'length' => 10),
            'totalAmount' => array('tipo'=>'float', 'length' => 100));

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }
}
