<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
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
            'productName' => array('tipo' => 'alphaNumber', 'length' => '100'),
            'measure'     => array('tipo' => 'string', 'length' => '100'),
            'price'       => array('tipo' => 'float', 'length' => '10'),
            'iva'         => array('tipo' => 'number', 'length' => '10'),
            'ieps'        => array('tipo' => 'float', 'length' => '5', 'minlength' => '1'),
            'currency'    => array('tipo' => 'string', 'length' => '100'),
            'description' => array('tipo' => 'text', 'length' => '1000'));

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
        $data['integradoId'] = JFactory::getSession()->get('integradoId',null,'integrado');

        $id_producto = $data['id_producto'];
        $save        = new sendToTimOne();

        unset($data['id_producto']);

        if($id_producto == 0){
            $save->saveProduct($data);
        }else{
            $save->updateProduct($data, $id_producto);
        }
        if(isset($this->integradoId)){
            $contenido = JText::_('NOTIFICACIONES_4');
            $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$data['nameIntegrado'].'</strong>',$contenido);
            $contenido = str_replace('$producto', '<strong style="color: #000000">'.$data['productName'].'</strong>',$contenido);
            $contenido = str_replace('$usuario', '<strong style="color: #000000">$'.$data['corrUser'].'</strong>',$contenido);
            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);

            $data['titulo']         = JText::_('TITULO_4');
            $data['body']           = $contenido;

            $send                   = new Send_email();
            $send->notification($data);
        }

        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=productoslist');
    }
}
