<?php
use Integralib\ReportFlujo;

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelFlujo extends JModelItem {

	protected $input;

	function __construct() {
		$month       = date('m');
		$year 		 = date('Y');
		$post 		 = array ('startDate' => 'STRING', 'endDate' => 'STRING', 'project' => 'INT');
		$this->input = JFactory::getApplication();
		$this->input = (object) $this->input->input->getArray( $post);
		$starDate 	 = date('d-m-Y', mktime(0,0,0, $month, 1, $year));
		$endDate 	 = date("d", mktime(0,0,0, $month+1, 0, $year)).'-'.$month.'-'.$year;

		$this->input->startDate = !is_null( $this->input->startDate ) ? strtotime( $this->input->startDate ) : strtotime($starDate);
		$this->input->endDate   = !is_null( $this->input->endDate )   ? strtotime( $this->input->endDate )   : strtotime($endDate);

		$session = JFactory::getSession();
		$this->input->integradoId = $session->get('integradoId', null, 'integrado');

		parent::__construct();
	}

	/**
	 * @param $vars
	 *
	 * @return ReportFlujo
	 */
	public function generateFlujo($vars) {
		$report = new ReportFlujo( $vars['integradoId'], $vars['startDate'], $vars['endDate'] );
		$report->startPeriod = $report->getFechaInicio();
		$report->endPeriod   = $report->getFechaFin();

		return $report;
	}
}

