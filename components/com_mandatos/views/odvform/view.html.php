<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvform extends JViewLegacy {
	
	function display($tpl = null){

        if (count($errors = $this->get('Errors'))) {
	        JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

		parent::display($tpl);
	}
}