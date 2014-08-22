<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ProyectosViewAltaproyectos extends JViewLegacy {
	
	function display($tpl = null){
		$input 		= JFactory::getApplication()->input;
		$data		= $input->getArray();
		
		if( isset($data['proyId']) ){
			$this->titulo = 'COM_PROYECTOS_EDICION_PROY_TITULO';
			$this->proyecto = $this->get('proyecto');
		}else{
			$this->titulo = 'COM_PROYECTOS_ALTA_PROY_TITULO';
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