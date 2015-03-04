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
	protected $data;

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
            'integradoId' => array('number' => true, 'maxlength' => 10),
            'cuenta'     => array('number' => true,    'maxlength' => 3),
            'referencia' => array('string' => true, 'maxlength' => 21),
            'date'       => array('fecha' => true,  'maxlength' => 10),
            'amount'     => array('float' => true,  'maxlength' => 20)
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
	    $this->data = JFactory::getApplication()->input->getArray($post);
        $save = new sendToTimOne();
        $changeDateType = new DateTime($this->data['date']);

        $this->data['date'] = $changeDateType->getTimestamp();

	    $this->verifyIntegrado();

	    $save->formatData($this->data);

	    if(is_null($this->data['id'])){
            $resultado = $save->insertDB('txs_banco_integrado');
        }else{
            $resultado = $save->updateDB('txs_banco_integrado',null,'id = '.$this->data['id']);
        }

        if($resultado){
	        $txTimone   = $this->makeTxTimone();
	        $id_tx_banco = $db->insertid();
	        $save       = $this->saveTxsRelation($save, $txTimone, $id_tx_banco);


	        $app->redirect('index.php?option=com_conciliacionbanco', JText::_('LBL_SAVED'), 'MESSAGE');
        }else{
            $app->enqueueMessage(JText::_('LBL_NO_SAVED'), 'WARNING');
        }

    }

	private function verifyIntegrado() {
		$integrados = Integrado::getAllIds();
		if (!array_key_exists($this->data['integradoId'], $integrados)) {
			// Si el id de integrado no es correcto, se asocia la TX con la Integradora
			$this->data['integradoId'] = 1;
		}
	}

	private function makeTxTimone() {
		//TODO: funcion de cash in en timone hacia la cuenta de integradora
		return time();
	}

	private function saveTxsRelation(sendToTimOne $save, $txTimone, $id_tx_banco) {
		$db = JFactory::getDbo();
		$return = true;

		try
		{
			$db->transactionStart();

				$query = $db->getQuery(true);

				$values = array($db->quote($txTimone), $db->quote(time()), $db->quote($this->data['integradoId']));

				$query->insert($db->quoteName('#__txs_timone_mandato'));
				$query->columns($db->quoteName(array('idTx', 'date', 'idIntegrado')));
				$query->values(implode(',',$values));

				$db->setQuery($query);
				$db->execute();
				$id_tx_timone = $db->insertid();

				$query = $db->getQuery(true);

				$values = array($db->quote($id_tx_banco), $db->quote($id_tx_timone));

				$query->insert($db->quoteName('#__txs_banco_timone_relation'));
				$query->columns($db->quoteName(array('id_txs_banco', 'id_txs_timone')));
				$query->values(implode(',',$values));

				$db->setQuery($query);
				$db->execute();

			$db->transactionCommit();
		}
		catch (Exception $e)
		{
			// catch any database errors.
			$db->transactionRollback();
//			JErrorPage::render($e);
			$return = false;
		}

		return $result;
	}
}