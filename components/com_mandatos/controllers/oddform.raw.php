<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOddform extends JControllerLegacy {

    protected $integradoId;

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
            'paymentMethod' => array('number' => true,  'maxlength' => 10 ,    'required' => true),
            'paymentDate'   => array('date' => true,   'maxlength' => 10,     'required' => true),
            'totalAmount'   => array('float' => true,   'maxlength' => 10,     'required' => true)
        );

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    private function sendNotifications($accion, $datos) {
        /*
         * Notificacion 20
         */
        $info = array();
        if($datos->paymentMethod==0){
            $metodoPago = JText::_('LBL_SPEI');
        }
        if($datos->paymentMethod) {
            $metodoPago = JText::_('LBL_CHEQUE');
        }

        $getCurrUser = new IntegradoSimple($this->integradoId);

        $arrayTitle = array($datos->numOrden);
        $array      = array($getCurrUser->getUsuarioPrincipal($this->integradoId)->name, $datos->numOrden, JFactory::getUser()->username, date('d-m-Y'),$datos->totalAmount, $metodoPago);

        $send                   = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);
        $info[] = $send->sendNotifications('20', $array, $arrayTitle);

        /*
         * Notificaciones 21
         */
        $titleAdmin = array($getCurrUser->getUsuarioPrincipal($this->integradoId), $datos->numOrden);

        $send                   = new Send_email();
        $send->setAdminEmails();
        $info[] = $send->sendNotifications('21', $array, $titleAdmin);
    }

    private function logEvent( $info, $dato ) {
        $logdata = $logdata = implode( ', ', array (
            JFactory::getUser()->id,
            $this->integradoId,
            __METHOD__,
            json_encode( array ( $info, $dato  ) )
        ) );
        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

    }


}
