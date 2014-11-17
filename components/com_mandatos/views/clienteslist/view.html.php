<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewClienteslist extends JViewLegacy {
	function display($tpl = null){
		$data 				= JFactory::getApplication()->input->getArray();
		$this->integradoId	= $data['integradoId'];
		
		$this->data = $this->get('clientes');
		$this->token = getFromTimOne::token();
		$this->catalogoBancos = $this->get('Catalogos');
        var_dump($this->catalogoBancos);
        exit;
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}
}