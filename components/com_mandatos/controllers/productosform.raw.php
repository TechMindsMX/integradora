<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
//TODO crear metodo de validación de campos para el formulario
class MandatosControllerProductosform extends JControllerLegacy {

    public function __construct(){
        $this->document     = JFactory::getDocument();
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post               = array(
            'productName' => 'STRING',
            'measure'     => 'STRING',
            'price'       => 'STRING',
            'iva'         => 'STRING',
            'ieps'        => 'STRING',
            'currency'    => 'STRING',
            'description' => 'STRING'
        );

        $this->parametros   = (object) $this->inputVars->getArray($post);

        parent::__construct();
    }

    function valida(){
        $document    = JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        $validacion  = new validador();
        $parametros  = $this->parametros;
        $diccionario = array(
            'productName' => array('alphaNumber'    => true,    'maxlength' => '100',  'notNull' => true),
            'measure'     => array('string'         => true,    'maxlength' => '100',  'notNull' => true),
            'price'       => array('float'          => true,    'maxlength' => '10',   'notNull' => true),
            'iva'         => array('number'         => true,    'maxlength' => '10',   'notNull' => true),
            'ieps'        => array('float'          => true,    'maxlength' => '5',    'minlength' => '1', 'max' => '100', 'min' => '0'),
            'currency'    => array('string'         => true,    'maxlength' => '100',  'notNull' => true),
            'description' => array('text'           => true,    'maxlength' => '1000', 'notNull' => true)
        );

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        foreach ($respuesta as $key => $value) {
            if($value !== true){
                echo json_encode($respuesta);
                return false;
            }
        }

        echo json_encode(array('redirect' =>'index.php?option=com_mandatos&view=productosform&id_producto=1'));
    }

    function saveProducts(){
        $campos      = array(
            'id_producto'=>'INT',
            'productName'=>'STRING',
            'measure'=>'STRING',
            'price'=>'STRING',
            'iva'=>'STRING',
            'ieps'=>'STRING',
            'currency'=>'STRING',
            'status'=>'STRING',
            'description'=>'STRING');

        $data                = $this->inputVars->getArray($campos);
        $data['price']       = round( (float)$data['price'] );
        $data['integradoId'] = JFactory::getSession()->get('integradoId',null,'integrado');

        $this->producto      = (object) $data;
        $id_producto = $data['id_producto'];
        $save        = new sendToTimOne();

        unset($data['id_producto']);

        if($id_producto == 0){
            $save->saveProduct($data);
            /*NOTIFICACIONES 4*/
            $getCurrUser         = new IntegradoSimple($this->producto->integradoId);

            $sendEmail  = new Send_email();
            $array = array($getCurrUser->user->name, $this->producto->productName, $getCurrUser->user->username, date( 'd-m-Y' ));

            $reportEmail	= $sendEmail->sendNotifications('4', $array, $getCurrUser->getUserPrincipal()->email);

        }else{
            $save->updateProduct($data, $id_producto);
        }

        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=productoslist');
    }
}
