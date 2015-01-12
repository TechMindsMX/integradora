<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.integrado');

class IntegradoViewIntegrado extends JViewLegacy {

	protected $integradoId;
	protected $permisos;

	function display($tpl = null)
	{
		$sesion = JFactory::getSession();
		$this->integradoId = $sesion->get('integradoId', null, 'integrado');

		$this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $this->integradoId);
		
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
