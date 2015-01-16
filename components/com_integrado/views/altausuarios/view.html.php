<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class IntegradoViewAltausuarios extends JViewLegacy {
    protected $permisos;
    protected $integradoId;

	function display($tpl = null)
	{
        $app                = JFactory::getApplication();
		$this->data 		= $this->get('Usuarios');
		$this->catalogos	= $this->get('catalogos');
		$this->integradoId  = $this->get('IntegradoId');

		// Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
        $this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $this->integradoId);

		parent::display($tpl);
	}
}