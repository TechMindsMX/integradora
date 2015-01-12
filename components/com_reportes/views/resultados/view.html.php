<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('integradora.integrado');

/**
 * HTML View class for the Reportes Component
 * @property mixed report
 * @property mixed integrado
 */
class ReportesViewResultados extends JViewLegacy
{
	// Overwriting JView display method
	protected $integradoId;

	function display($tpl = null){
		$sesion = JFactory::getSession();
		$this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $integrado                    = new IntegradoSimple($this->integradoId);
        $this->integrado              = $integrado->integrados[0];
        $this->integrado->displayName = $integrado->getDisplayName();
        $this->reporte                = $this->get('Reporte');
        $this->proyectos              = $this->get('Proyectos');

		if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}

		// Display the view
		parent::display($tpl);
	}


}