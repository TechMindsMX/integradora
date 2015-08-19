<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('integradora.integrado');
jimport('html2pdf.reportecontabilidad');
jimport('integradora.gettimone');

/**
 * HTML View class for the Reportes Component
 * @property mixed report
 * @property mixed integrado
 */
class ReportesViewReportecontabilidad extends JViewLegacy
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
        $ordenes                            = new getFromTimOne();

        $this->odv                      = $ordenes->getOrdenesVenta();
        $createPdf                          = new reportecontabilidad();



        foreach($this->odv as $orden){
            $createPdf->createPDF($orden, 'odv');
        }


        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}

        $this->loadHelper('Mandatos');

        $this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $this->integradoId);
		// Display the view
		parent::display($tpl);
	}


}