<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOddpreview extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray();
		$this->integradoId 	= $data['integradoId'];

		$this->odd		 	= $this->get('ordenes');

		$this->integCurrent = $this->get('integrado');

var_dump($this->integCurrent->integrados);		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}