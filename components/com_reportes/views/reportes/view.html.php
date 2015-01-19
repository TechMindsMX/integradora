<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Reportes Component
 */
class ReportesViewReportes extends JViewLegacy
{
	protected $integradoId;

	function display($tpl = null)
	{
		$sesion = JFactory::getSession();
		$this->integradoId = $sesion->get('integradoId', null, 'Integrado');

		$this->data 		= $this->get('Solicitud');
        $this->catalogos 	= $this->get('catalogos');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}
		// Display the view
		parent::display($tpl);
	}
}