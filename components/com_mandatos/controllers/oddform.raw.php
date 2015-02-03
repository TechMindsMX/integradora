<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOddform extends JControllerLegacy {

    function saveODD() {
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray();
        $datos              = json_decode($this->parametros['datos']);
        $save               = new sendToTimOne();
        $date               = new DateTime($datos->paymentDate);
        $session            = JFactory::getSession();
        $datos->paymentDate = $date->getTimestamp();

        unset($datos->view, $datos->option, $datos->Itemid, $datos->confirmacion);
        $this->integradoId = $session->get('integradoId', null, 'integrado');

        $datos->integradoId = $this->integradoId;

        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__, $datos->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }

	    if($datos->id == '') {
		    $datos->createdDate = time();
		    $datos->numOrden = $save->getNextOrderNumber('odd', $datos->integradoId);
		    unset($datos->id);
            $datos->status=1;
		    $save->formatData($datos);
		    $salvado = $save->insertDB('ordenes_deposito', null, null, true);
            $this->sendNotifications('generado', $datos);
        }else{
            $save->formatData($datos);
            $save->updateDB('ordenes_deposito', null,'id = '.$datos->id);
            $salvado = $datos->id;
            $this->sendNotifications('actualizado', $datos);
        }

        if($salvado) {
            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&view=oddpreview&idOrden=' . $salvado.'&success=true',
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
        $diccionario = array(
            'paymentMethod' => array('number' => true,  'maxlength' => 10 ,    'notNull' => true),
            'paymentDate'   => array('date' => true,   'maxlength' => 10,     'notNull' => true),
            'totalAmount'   => array('float' => true,   'maxlength' => 10,     'notNull' => true)
        );

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    private function sendNotifications($accion, $datos) {
        $data[0] = '<table>';
        $data[2] = '</table>';
        foreach ( $datos as $key => $value ) {
            $data[] = '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
        }

        $titulo = JText::_('TITULO_19');

        $contenido = JText::sprintf('NOTIFICACIONES_19', $accion, implode($data) );

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $dato['email']          = JFactory::getUser()->email;
        $send                   = new Send_email();
        $info = $send->notification($dato);

        $integradoAdmin     = new IntegradoSimple(93);

        $titulo = JText::_('TITULO_19A');

        $contenido = JText::sprintf('NOTIFICACIONES_19A', $accion, implode($data) , JFactory::getUser()->username);

        $datoAdmin['titulo']         = $titulo;
        $datoAdmin['body']           = $contenido;
        $datoAdmin['email']          = $integradoAdmin->user->email;
        $send                   = new Send_email();
        $infoAdmin = $send->notification($datoAdmin);

    }

    protected $integradoId;


}
