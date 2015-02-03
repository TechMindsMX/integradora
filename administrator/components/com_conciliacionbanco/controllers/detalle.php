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

class conciliacionbancoControllerdetalle extends JControllerAdmin{
	public function confirmacion(){
        $post = array(
            'id'            => 'INT',
            'confirmacion'  => 'INT',
            'integradoId'   => 'INT',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $validaciones = new validador();
        $data = JFactory::getApplication()->input->getArray($post);

        $diccionario = array(
            'integradoId' => array('number' => true, 'length'=>10),
            'cuenta'     => array('number' => true,    'length'=>3),
            'referencia' => array('string' => true, 'length'=>21),
            'date'       => array('fecha' => true,  'length'=>10),
            'amount'     => array('float' => true,  'length'=>20)
        );
        $resultadovalidacion = $validaciones->procesamiento($data,$diccionario);


	}

    public function save(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $post = array(
            'id'            => 'int',
            'integradoId'   => 'INT',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $data = JFactory::getApplication()->input->getArray($post);
        $save = new sendToTimOne();
        $changeDateType = new DateTime($data['date']);

        $data['date'] = $changeDateType->getTimestamp();

        $save->formatData($data);

        if(is_null($data['id'])){
            $resultado = $save->insertDB('txs_banco_integrado');
        }else{
            $resultado = $save->updateDB('txs_banco_integrado',null,'id = '.$data['id']);
        }

        if($resultado){
            $app->redirect('index.php?option=com_conciliacionbanco', JText::_('LBL_SAVED'), 'MESSAGE');
        }else{
            $app->enqueueMessage(JText::_('LBL_NO_SAVED'), 'WARNING');
        }

    }
}