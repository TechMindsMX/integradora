<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class ProyectosViewAltasubproyectos extends JViewLegacy {
	
	function display($tpl = null){
		$input 		= JFactory::getApplication()->input;
		$data		= $input->getArray();
		$this->token = getFromTimOne::token();
		
		if( isset($data['proyId']) ){
			$this->titulo = 'COM_PROYECTOS_EDICION_SUB_TITULO';
			$this->data = $this->get('proyectos');
			$this->proyecto = $this->get('proyecto');
		}else{
			$this->titulo = 'COM_PROYECTOS_ALTA_SUB_TITULO';
			$this->data = $this->get('proyectos');
		}
		
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }
		parent::display($tpl);
	}
}