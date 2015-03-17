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
    protected $data;
    private $receptor;

    public function __construct(){
        $this->encoding = JFactory::getDocument()->setMimeEncoding('application/json');

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
        $data = JFactory::getApplication()->input->getArray($post);

        $diccionario = array(
            'integradoId' => array('number'       => true, 'maxlength' => 10),
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
}