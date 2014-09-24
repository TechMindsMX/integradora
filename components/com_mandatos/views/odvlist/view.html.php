<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvlist extends JViewLegacy {
	function display($tpl = null){
		$data 				= JFactory::getApplication()->input->getArray();
		$this->integradoId	= $data['integradoId'];
		
		$this->data         = $this->get('ordenes');
		$this->token        = getFromTimOne::token();
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		
		$this->loadHelper('Mandatos');

		foreach ($this->data as $key => $odv) {
			$odv->proveedor = MandatosHelper::getProviderFromID($odv->clientId, $this->integradoId);
			
			$this->data[$key] = $odv;
		}
		
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}