<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewMandatos extends JViewLegacy {
	
	function display($tpl = null){
		$data				= JFactory::getApplication()->input->getArray();
		$this->data 		= $this->get('Solicitud');
		$this->integradoId	= $data['integradoId'];
		$this->catalogos 	= $this->get('catalogos');
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		parent::display($tpl);
	}
}