<?php
/**
 * @version     1.0.1
 * @package     com_donde_comprar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      ismael <aguilar_2001@hotmail.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.validator');
jimport('integradora.gettimone');

class AdminintegradoraControllerValidacionForms extends JControllerAdmin{
    public $input;
    public $encoding;
    protected $data;

    public function __construct(){
        $this->encoding = JFactory::getDocument()->setMimeEncoding('application/json');
        $this->input = JFactory::getApplication()->input;

        parent::__construct();

    }

    public function validatx(){
        $this->encoding;
        $post = array(
            'id'            => 'STRING',
            'confirmacion'  => 'STRING',
            'integradoId'   => 'STRING',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'STRING'
        );
        $validaciones = new validador();
        $data = $this->input->getArray($post);

        $diccionario = array(
            'integradoId' => array('alphaNumber'  => true, 'maxlength' => 32),
            'cuenta'      => array('number'       => true, 'maxlength' => 3,  'required' => true),
            'referencia'  => array('alphaNumber'  => true, 'maxlength' => 21, 'required' => true),
            'date'        => array('date'         => true, 'maxlength' => 10, 'required' => true),
            'amount'      => array('float'        => true, 'maxlength' => 20, 'required' => true)
        );

        $resultadovalidacion = $validaciones->procesamiento($data,$diccionario);

        foreach ($resultadovalidacion as $value) {
            if(is_array($value)){
                $resultadovalidacion['success'] = false;

                echo json_encode($resultadovalidacion);
                die;
            }
        }

        $resultadovalidacion['success'] = true;

        echo json_encode($resultadovalidacion);
    }

    public function validaparams(){
        $this->encoding;
        $filtroPost   = array('params' => 'STRING');
        $params       = $this->input->getArray($filtroPost);
        $validaciones = new validador();
        $diccionario  = array(
            'params' => array('number' => true, 'max' => 5, 'min'=>1, 'required' => true)
        );

        $isValid = $validaciones->procesamiento($params,$diccionario);

        if( is_array($isValid['params']) ){
            $isValid['success'] = false;

            echo json_encode($isValid);
            die;

        }

        $isValid['success'] = true;

        echo json_encode($isValid);
    }
}