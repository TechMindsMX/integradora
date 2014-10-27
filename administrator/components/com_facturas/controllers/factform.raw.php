<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

class FacturasControllerFactform extends JControllerAdmin {

    public function __construct($config = array()) {
        $get            = JFactory::getApplication()->input;
        $params         = array('banco'       => 'STRING',
                                'cuenta'      => 'STRING',
                                'ordenPagada' => 'STRING',
                                'reference'   => 'STRING',
                                'reference'   => 'STRING',
                                'amount'      => 'STRING',
                                'paymentDay'  => 'STRING');
        $this->data     = $get->getArray($params);
        $this->document =  $document = JFactory::getDocument();
        parent::__construct($config);
    }

    function safeForm () {
        $this->document->setMimeEncoding('application/json');
        $validador = new validador();
        $diccionario = array('banco'       => array('tipo'=>'number',             'length' => '2',  'notNull' => 'true'),
                             'cuenta'      => array('tipo'=>'number',             'length' => '2',  'notNull' => 'true'),
                             'ordenPagada' => array('tipo'=>'number',             'length' => '2',  'notNull' => 'true'),
                             'reference'   => array('tipo'=>'referenciaBancaria', 'length' => '21', 'notNull' => 'true'),
                             'amount'      => array('tipo'=>'float',              'length' => '15', 'notNull' => 'true'),
                             'paymentDay'  => array('tipo'=>'date',               'length' => '10', 'notNull' => 'true'));

        $respuesta = $validador->procesamiento($this->data,$diccionario);

        foreach ($respuesta as $value) {
            if(is_array($value)){
                echo json_encode($respuesta);
                return;
            }
        }


    }
}