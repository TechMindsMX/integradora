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
        $respuesta = $this->makeValidations();

        foreach ($respuesta as $key => $value) {
            if($value !== true){
                echo json_encode($respuesta);
                return false;
            }
        }

        $document = JFactory::getDocument();
        $document->setMimeEncoding( 'application/json' );
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

var_dump($this->makeValidations());exit;
        if($this->makeValidations()) {
            if($id_producto == 0){
                $save->saveProduct($data);
                $this->sendEmail();


            }else{
                $save->updateProduct($data, $id_producto);
            }

        }

        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=productoslist');
    }

    public function sendEmail()
    {
        /*NOTIFICACIONES 4*/
        $getCurrInteg = new IntegradoSimple($this->producto->integradoId);

        $sendEmail = new Send_email();
        $array = array($getCurrInteg->user->name, $this->producto->productName, $getCurrInteg->user->username, date('d-m-Y'));

        $sendEmail->setIntegradoEmailsArray( $getCurrInteg );

        $reportEmail = $sendEmail->sendNotifications('4', $array);

    }

    /**
     * @return array
     */
    private function makeValidations() {
        $validacion  = new validador();
        $parametros  = $this->parametros;
        $diccionario = array (
            'productName' => array ( 'alphaNumber' => true, 'maxlength' => '100', 'required' => true ),
            'measure'     => array ( 'string' => true, 'maxlength' => '100', 'required' => true ),
            'price'       => array ( 'float' => true, 'maxlength' => '10', 'required' => true ),
            'iva'         => array ( 'number' => true, 'maxlength' => '10', 'required' => true ),
            'ieps'        => array ( 'float'     => true,
                                     'maxlength' => '5',
                                     'minlength' => '1',
                                     'max'       => '100',
                                     'min'       => '0'
            ),
            'currency'    => array ( 'string' => true, 'maxlength' => '100', 'required' => true ),
            'description' => array ( 'text' => true, 'maxlength' => '1000', 'required' => true )
        );

        $respuesta = $validacion->procesamiento( $parametros, $diccionario );

        return $respuesta;
    }

}
