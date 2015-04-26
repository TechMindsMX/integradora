<?php
use Integralib\ReportResultados;

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelResultados extends JModelItem {

    protected $input;

    function __construct() {

	    $this->input            = (object) JFactory::getApplication()->input->getArray( array (
		                                                                                    'startDate'   => 'STRING',
		                                                                                    'endDate'     => 'STRING',
		                                                                                    'project'     => 'INT'
	                                                                                    ) );
	    $this->input->startDate   = ! is_null( $this->input->startDate ) ? strtotime( $this->input->startDate ) : null;
	    $this->input->endDate     = ! is_null( $this->input->endDate ) ? strtotime( $this->input->endDate ) : null;

	    $session = JFactory::getSession();
	    $this->input->integradoId = $session->get('integradoId', null, 'integrado');

	    parent::__construct();
    }

	/**
	 * @return ReportResultados
	 */
	public function getReporte(){

		$report      = new ReportResultados($this->input->integradoId , $this->input->startDate, $this->input->endDate, $this->input->project);
		$report->calculateIngresos();
		$report->calculateEgresos();
		$report->startPeriod = $report->getFechaInicio();
		$report->endPeriod   = $report->getFechaFin();

		return $report;
	}

}