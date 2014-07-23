<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewIntegrado extends JViewLegacy {
	
	function display($tpl = null)
	{
		$this->data = $this->get('Display');
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		
		parent::display($tpl);
	}
}
