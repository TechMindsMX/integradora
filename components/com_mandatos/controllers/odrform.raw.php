<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdrform extends JControllerLegacy {

    protected $diccionario;
    private $integradoId;

    public function __construct(){
        $this->session = JFactory::getSession();
        $this->integradoId = $this->session->get('integradoId', null, 'integrado');
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post = array(
            'paymentDate'   => 'STRING',
            'paymentMethod' => 'STRING',
            'totalAmount'   => 'FLOAT',
            'idOrden'       => 'INT'
	    );

        $this->parametros   = $this->inputVars->getArray($post);

        $this->diccionario = array(
            'totalAmount'   => array('float' => true,  'maxlength' => 10,    'required' => true),
            'paymentDate'   => array('date' => true,   'maxlength' => 10,    'required' => true),
            'paymentMethod' => array('number' => true, 'maxlength' => 1,     'required' => true)
        );

        parent::__construct();
    }

    function saveODR() {

        //validacion
        $validaciones = MandatosHelper::valida($this->parametros, $this->diccionario);

        $saldoValidacion = $this->enoughFunds();
        $validaciones['totalAmount'] = $saldoValidacion['totalAmount'];

        foreach ( $validaciones as $key => $check ) {
            if ( is_array( $check ) ) {
                $errores[ $key ] = ' ' . $check['msg'] . $key . ', ';
            }
        }
        if ( isset( $errores ) ) {

            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&task=odrform.redirectWithMsg&format=raw',
                               'redireccion' => true);
            echo json_encode( $respuesta );

            return false;
        }
        //validacion

        $datos = $this->parametros;
        $save  = new sendToTimOne();
        $date  = new DateTime($datos['paymentDate']);

        $datos['paymentDate'] = $date->getTimestamp();
        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__,  $this->integradoId);

        if(!$this->permisos['canEdit']) {
            // acciones cuando NO tiene permisos para Editar/Crear
            $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=odrlist'), JText::_('LBL_CANT_EDIT'), 'error');
            die;
        }

        $datos['integradoId'] = $this->integradoId;

        if( $datos['idOrden'] == 0) {
            $idOrden = $datos['idOrden'];
            unset($datos['idOrden']);

            $datos['createdDate'] = time();
            $datos['numOrden'] = $save->getNextOrderNumber('odr',  $this->integradoId);
            $datos['status'] = 1;
            $save->formatData($datos);

            $salvado = $save->insertDB('ordenes_retiro', null, null, true);

            $idOrden = $salvado;
        }else{
            $idOrden = $datos['idOrden'];
            unset($datos['idOrden']);
            $save->formatData($datos);
            $salvado = $save->updateDB('ordenes_retiro', null,'id = '.$idOrden);
        }

        if($salvado) {
            $this->session->set('msg','Datos Almacenados', 'odr');
	        $this->session->clear('data', 'odr');

            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&view=odrpreview&idOrden=' . $idOrden.'&success=true',
                'redireccion' => true);
        }else{
            $respuesta = array('redireccion' => false);
        }
        $this->sendEmail($datos);

        JFactory::getDocument()->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    function valida(){

        $validacionFunds = $this->enoughFunds();

        if( $validacionFunds['totalAmount'] === true ) {
            $validacion = array_merge($validacionFunds, $this->validaDatos());
        } else {
            $validaDatos = $this->validaDatos();
            unset($validaDatos['totalAmount']);
            $validacion = array_merge($validacionFunds, $validaDatos);
        }

	    $this->session->set('data',json_encode($this->parametros), 'odr');

        $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode($validacion) ) );
        JLog::add($logdata, JLog::DEBUG, 'bitacora');

        $document = JFactory::getDocument();
	    $document->setMimeEncoding('application/json');
        echo json_encode($validacion);
    }

	private function getBalance() {
		$model = $this->getModel('odrform');
		$balance = $model->getBalance();

		return $balance;
	}

	private function enoughFunds() {
		$respuesta = array('totalAmount' => true);
		if($this->parametros['totalAmount'] > $this->getBalance()) {
			$respuesta = array('totalAmount' => array('success' => false, 'msg' => 'SALDO INSUFICIENTE'));
		}
		return $respuesta;
	}

	private function validaDatos() {
		$validacion = new validador();

		$this->getBalance();

		$parametros = $this->parametros;

		$respuesta = $validacion->procesamiento($parametros,$this->diccionario);

		return $respuesta;
	}

    public function redirectWithMsg() {
        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odrlist', 'LBL_ERROR' ,'error');
    }

    private function sendEmail($datos)
    {
        /*
         * NOTIFICACIONES 18
         */
        $getCurrUser    = new IntegradoSimple($this->integradoId);
        $titleArray     = array($datos['numOrden']);
        $array          = array($getCurrUser->getUserPrincipal()->name, $datos['numOrden'], JFactory::getUser()->username, date('d-m-Y'), $datos['totalAmount'], 'Cuenta' );

        $send                   = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);
        $info           = $send->sendNotifications('18', $array, $titleArray);
    }

}
