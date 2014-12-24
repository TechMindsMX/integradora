<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelBalance extends JModelItem {

	protected $cancelUrl;

	public function getBalance($vars) {

		$r = new ReportBalance( array('balanceId'=>$vars['id'], 'integradoId' => $vars['integradoId']) );
		$r->getExistingBalance();
		$report = $r;

		var_dump($report);exit;
		return $report;
	}

	public function generateBalance($vars) {
		$r = new ReportBalance( array('balanceId'=>null, 'integradoId' => $vars['integradoId']) );
		$r->generateBalance();
		$report = $r;

		var_dump($report);exit;
		return $report;
	}
}