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
	// Overwriting JView display method
	function display($tpl = null)
	{
        $integrado	 		= new Integrado;

        $data				= JFactory::getApplication()->input->getArray();
        $this->data 		= $this->get('Solicitud');
        $this->integradoId	= isset($integrado->integrados[0]) ? $integrado->integrados[0]->integrado_id : $data['integradoId'];
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