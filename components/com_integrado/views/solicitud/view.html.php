<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewSolicitud extends JViewLegacy {
	
	function display($tpl = null)
	{
		$this->data = $this->get('solicitud');

		$this->catalogos = $this->get('catalogos');

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
        	Log::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
        	return false;
        }

		parent::display($tpl);
	}
}