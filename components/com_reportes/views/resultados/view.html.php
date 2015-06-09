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
	public $integradora;
	protected $integradoId;
    protected $permisos;
	protected $reporte;
	protected $proyectos;

	function display($tpl = null){
		$sesion = JFactory::getSession();
        $integradora = new \Integralib\Integrado();
		$this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $this->integrado                    = new IntegradoSimple($this->integradoId);
        $this->integradora                  = new IntegradoSimple($integradora->getIntegradoraUuid());
        $this->reporte                      = $this->get('Reporte');
        $this->proyectos                    = $this->get('Proyectos');

		if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}

        $this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $this->integradoId);
		// Display the view
		parent::display($tpl);
	}


}