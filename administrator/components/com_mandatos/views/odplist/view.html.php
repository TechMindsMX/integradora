<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdplist extends JViewLegacy {
	function display($tpl = null){
        $this->integradora = new \Integralib\Integrado();
		$data   	       = $this->get('InputData');
        $data->integradoId = JFactory::getSession()->get('integradoId', $this->integradora->getIntegradoraUuid(), 'integrado');
        $this->integradoId = $data->integradoId;
		$this->ordenes     = $this->get('ordenes');
        $this->mutuo       = $this->get('DataMutuo');
		$this->token       = getFromTimOne::token();

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);
		
		parent::display($tpl);
	}
}