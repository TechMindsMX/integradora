<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdrform extends JControllerLegacy {

    private $integradoId;

    public function __construct(){
        $session = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null, 'integrado');
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post = array(
            'paymentDate'   => 'STRING',
            'paymentMethod' => 'STRING',
            'totalAmount'   => 'STRING'
	    );

        $this->parametros   = $this->inputVars->getArray($post);
        $this->parametros['integradoId'] = $this->integradoId;
        parent::__construct();
    }

    function saveODR() {
        $datos = $this->parametros;
        $save  = new sendToTimOne();
        $date  = new DateTime($datos['paymentDate']);

        $datos['paymentDate'] = $date->getTimestamp();
        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__,  $this->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }
        if( !isset($datos['id']) ) {
            $datos['createdDate'] = time();
            $datos['numOrden'] = $save->getNextOrderNumber('odr',  $this->integradoId);
            $datos['status'] = 1;
            $save->formatData($datos);

            $salvado = $save->insertDB('ordenes_retiro', null, null, true);
        }else{
            $save->formatData($datos);
            $salvado = $save->updateDB('ordenes_retiro', null,'numOrden = '.$datos['id']);
        }

        if($salvado) {
            $sesion = JFactory::getSession();
            $sesion->set('msg','Datos Almacenados', 'odrCorrecta');

            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&view=odrpreview&idOrden=' . $salvado.'&success=true',
                'redireccion' => true);
        }else{
            $respuesta = array('redireccion' => false);
        }


        JFactory::getDocument()->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    function valida(){

	    $respuesta = $this->validaSaldo();

	    if(!$respuesta['totalAmount']) {
		    $respuesta = $this->validaDatos();
	    }

	    $document = JFactory::getDocument();
	    $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

	private function getBalance() {
		$model = $this->getModel('odrform');
// cambiar esto por el balance del usuario
		$balance = $model->getBalance();

		return $balance;
	}

	private function validaSaldo() {
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

		$diccionario = array(
			'amount'        => array('tipo' => 'float',  'length' => 10,    'notNull' => true),
			'paymentDate'   => array('tipo' => 'date',   'length' => 10,    'notNull' => true),
			'paymentMethod' => array('tipo' => 'number', 'length' => 1,     'notNull' => true)
		);

		$respuesta = $validacion->procesamiento($parametros,$diccionario);

		return $respuesta;
	}

}
