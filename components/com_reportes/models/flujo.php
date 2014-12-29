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

	public function getFlujo($vars) {
		$r = new ReportFlujo( $vars['id'], $vars['integradoId'], $vars['startDate'], $vars['endDate'] );
		$r->getIngresos();
		$r->getEgresos();
		$report = $r;

		return $report;
	}

	public function generateFlujo($vars) {
		$r = new ReportFlujo( $vars['id'], $vars['integradoId'], $vars['startDate'], $vars['endDate'] );
		$r->getIngresos();
		$r->getEgresos();
		$report = $r;

		return $report;
	}
}