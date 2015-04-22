<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdcform extends JControllerLegacy {

    public function __construct(){
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post = array('idOrden'  => 'INT',
                'numOrden'       => 'INT',
                'proyecto'       => 'STRING',
                'proveedor'      => 'STRING',
                'paymentDate'    => 'STRING',
                'paymentMethod'  => 'STRING',
                'totalAmount'    => 'STRING',
                'urlXML'         => 'STRING',
                'observaciones'  => 'STRING',
                'bankId'         => 'INT'
            );

        $this->parametros   = $this->inputVars->getArray($post);

	    $request = new \Integralib\TimOneRequest();
	    $xmlFileData            = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->parametros->urlXML);
	    $data 			        = new xml2Array();
	    $factura                = $data->manejaXML($xmlFileData);

	    // TODO: validación del xml que se sube en la plataforma, ACTIVAR
//	    $request->sendValidateInvoice( Factura::getXmlUUID($factura) );

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    function saveODC() {
        $db = JFactory::getDbo();

        $datos = $this->parametros;
        $save  = new sendToTimOne();
        $date  = new DateTime($datos['paymentDate']);
        $id    = $datos['idOrden'];

        $datos['paymentDate'] = $date->getTimestamp();

        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }

        if( $id === 0 ) {
            unset($datos['idOrden']);
            $datos['createdDate'] = time();
            $datos['numOrden'] = $save->getNextOrderNumber('odc', $this->integradoId);
            $datos['status'] = 1;
            $datos['integradoId'] = $this->integradoId;

            $save->formatData($datos);
            $salvado = $save->insertDB('ordenes_compra');

            $id = $db->insertid();

            $this->sendNotifications($datos);
        }else{
            unset($datos['idOrden']);
            $save->formatData($datos);
            $salvado = $save->updateDB('ordenes_compra', null,'numOrden = '.$datos['numOrden']);
        }

        if($salvado) {
            $sesion = JFactory::getSession();
            $sesion->set('msg','Datos Almacenados', 'odcCorrecta');

            $respuesta = array(
                'urlRedireccion' => 'index.php?option=com_mandatos&view=odcpreview&idOrden=' . $id .'&success=true',
                'redireccion' => true
            );
        }else{
            $respuesta = array('redireccion' => false);
        }


        JFactory::getDocument()->setMimeEncoding('application/json');
        $respuesta['idOrden']= $id;

        echo json_encode($respuesta);
    }

    function valida(){
        $validacion = new validador();
        $document = JFactory::getDocument();

        $parametros = $this->parametros;

        $diccionario = array(
            'integradoId'   => array('number' => true,  'maxlength' => 10),
            'numOrden'      => array('number' => true,  'maxlength' => 10),
            'proveedor'     => array('number' => true,  'maxlength' => 10, 'required' => true),
            'proyecto'      => array('number' => true,  'maxlength' => 10, 'required' => true),
            'paymentDate'   => array('date'   => true,  'maxlength' => 10, 'required' => true),
            'paymentMethod' => array('number' => true,  'maxlength' => 10),
            'bankId'        => array('number' => true,  'required' => true),
            'observaciones' => array('text'   => true,  'maxlength' => 1000));

        $respuesta = $validacion->procesamiento($parametros,$diccionario);
        $respuesta['proveedor'] = $parametros['proveedor'] == 0 ? array('success'=>false,'msg'=>'Seleccione el proveedor') : $respuesta['proveedor'];

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    private function sendNotifications($datos)
    {
        $info = array();
        /*
         * NOTIFICACIONES 11
         */

        $nameProveedor = $this->getNameProveedor();
        $getIntegradoSimple = new IntegradoSimple($this->integradoId);

        $arrayTitle = array($datos['numOrden']);
        $array = array($getIntegradoSimple->user->username, $datos['numOrden'],  JFactory::getUser()->name, date('d-m-Y'), $this->parametros['totalAmount'], $nameProveedor);

        $send = new Send_email();
        $send->setIntegradoEmailsArray($getIntegradoSimple);

        $info[] = $send->sendNotifications('11', $array, $arrayTitle);

        /*
         * Notificaciones 12
         */
        $arrayTitleAdmin = array($datos['numOrden'], date('d-m-Y'), $nameProveedor, $this->parametros['totalAmount'], $getIntegradoSimple->user->username, JFactory::getUser()->name);

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('12', $array, $arrayTitleAdmin);
    }

    /**
     * @return $nameProveedor
     */
    private function getNameProveedor()
    {
        $proveedores = getFromTimOne::getClientes($this->integradoId, 1);

        foreach ($proveedores as $key => $value) {
            if ($value->id == $this->parametros['proveedor']) {
                $nameProveedor = $value->corporateName;
            }
        }
        return $nameProveedor;
    }
}
