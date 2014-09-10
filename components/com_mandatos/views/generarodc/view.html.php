<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewGenerarodc extends JViewLegacy {
	
	function display($tpl = null){
		$data				= JFactory::getApplication()->input->getArray();
		$this->integradoId	= $data['integradoId'];
		$this->data 		= $this->get('orden');
		$this->proyectos 	= $this->get('proyectos');
		$this->proveedores	= $this->get('providers');
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		parent::display($tpl);
	}
}