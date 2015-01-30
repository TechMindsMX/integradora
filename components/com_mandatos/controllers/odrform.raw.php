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
        $session = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null, 'integrado');
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post = array(
            'paymentDate'   => 'STRING',
            'paymentMethod' => 'STRING',
            'totalAmount'   => 'FLOAT',
            'idOrden'       => 'INT'
	    );

        $this->parametros   = $this->inputVars->getArray($post);
        $this->parametros['integradoId'] = $this->integradoId;

        $this->diccionario = array(
            'totalAmount'   => array('tipo' => 'float',  'length' => 10,    'notNull' => true),
            'paymentDate'   => array('tipo' => 'date',   'length' => 10,    'notNull' => true),
            'paymentMethod' => array('tipo' => 'number', 'length' => 1,     'notNull' => true)
        );

        parent::__construct();
    }

    function saveODR() {

        //validacion
        $validaciones = MandatosHelper::valida($this->parametros, $this->diccionario);

        $saldoValidacion = $this->validaSaldo();
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

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }

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
            $sesion = JFactory::getSession();
            $sesion->set('msg','Datos Almacenados', 'odrCorrecta');

            $respuesta = array('urlRedireccion' => 'index.php?option=com_mandatos&view=odrpreview&idOrden=' . $idOrden.'&success=true',
                'redireccion' => true);
        }else{
            $respuesta = array('redireccion' => false);
        }
        /*NOTIFICACIONES 17*/
        $integradoSimple     = new IntegradoSimple($this->integradoId);
        $getCurrUser         = new Integrado($this->integradoId);

        $titulo = JText::_('TITULO_17');

        $contenido = JText::_('NOTIFICACIONES_17');

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $dato['email']          = $getCurrUser->user->email;
        $send                   = new Send_email();
        $info = $send->notification($dato);


        JFactory::getDocument()->setMimeEncoding('application/json');
        echo json_encode($respuesta);
        exit;
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

        $logdata = $logdata = implode(', ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode($validacion) ) );
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

}
