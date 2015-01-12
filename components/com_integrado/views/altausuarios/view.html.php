<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class IntegradoViewAltausuarios extends JViewLegacy {
	
	function display($tpl = null)
	{
		$this->data 		= $this->get('Usuarios');
		$this->catalogos	= $this->get('catalogos');
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}
}