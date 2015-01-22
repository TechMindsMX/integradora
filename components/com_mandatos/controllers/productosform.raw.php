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
            'ieps'        => array('tipo' => 'float', 'length' => '100'),
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
}
