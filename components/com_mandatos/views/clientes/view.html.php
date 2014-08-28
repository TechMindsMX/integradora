<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewClientes extends JViewLegacy {
	function display($tpl = null){
		$this->data = $this->get('clientes');
		$this->token = getFromTimOne::token();
	
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}
}