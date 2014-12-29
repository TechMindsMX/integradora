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

		$r = new ReportFlujo( $vars['id'], $vars['integradoId'], $vars['inicio'], $vars['fin'] );
		$r->getIngresos();
		$r->getEgresos();
		$report = $r;

		var_dump(__METHOD__,$report);
		return $report;
	}

	public function generateFlujo($vars) {
		$r = new ReportFlujo( $vars['id'], $vars['integradoId'], $vars['inicio'], $vars['fin'] );
		$r->getIngresos();
		$r->getEgresos();
		$report = $r;

		var_dump(__METHOD__,$report);
		return $report;
	}
}