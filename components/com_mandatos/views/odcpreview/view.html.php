<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcpreview extends JViewLegacy {
	
	function display($tpl = null){
		$data				= JFactory::getApplication()->input->getArray();
		$this->integradoId	= $data['integradoId'];
		$this->proveedores	= $this->get('providers');
		
		$this->odc		 		= $this->get('ordenes');

		$this->integCurrent = $this->get('integrado')->integrados[0];
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		parent::display($tpl);
	}
}