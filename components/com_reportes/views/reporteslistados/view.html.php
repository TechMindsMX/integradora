<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Reportes Component
 */
class ReportesViewReporteslistados extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{

	    $this->data             = $this->get('Solicitud');
        $this->balances         = $this->get('Balance');
        $this->flujo            = $this->get('Flujo');
        $this->resultados       = $this->get('Resultados');


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