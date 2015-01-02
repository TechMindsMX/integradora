<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelFlujo extends JModelItem {

	protected $cancelUrl;

	public function generateFlujo($vars) {
		$r = new ReportFlujo( $vars['integradoId'], $vars['startDate'], $vars['endDate'] );
		$r->getIngresos();
		$r->getEgresos();
		$r->getDepositos();
		$r->getRetiros();
		$r->getPrestamos();
		$report = $r;

		return $report;
	}
}