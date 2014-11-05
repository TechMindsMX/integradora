<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewMandatos extends JViewLegacy {
	
	function display($tpl = null){
		$this->data 		= $this->get('Integrados');

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		parent::display($tpl);
	}
}