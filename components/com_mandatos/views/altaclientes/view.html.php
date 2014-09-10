<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewAltaclientes extends JViewLegacy {
	
	function display($tpl = null){
		$input 				= JFactory::getApplication()->input;
		$data				= $input->getArray();
		$this->integradoId 	= $data['integradoId'];
		
		if( isset($data['clientId']) ){
			$this->titulo = 'COM_MANDATOS_CLIENT_LBL_EDITAR';
			//$this->producto = $this->get('producto');
		}else{
			$this->titulo = 'COM_MANDATOS_CLIENT_LBL_AGREGAR';
		}
		
		$this->catalogos = $this->get('catalogos');
		
		$this->token = getFromTimOne::token();
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }
		parent::display($tpl);
	}
}