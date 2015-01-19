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

	public function generateBalance($vars) {
		$r = new ReportBalance( array('balanceId'=>null, 'integradoId' => $vars['integradoId']) );
		$r->generateBalance();
		$report = $r;

		$rtxs = new ReportBalanceTxs( array('balanceId'=>$vars['id'], 'integradoId' => $vars['integradoId']) );
		$rtxs->generateBalance($vars['year']);
		var_dump($report, $rtxs);exit;
		return $report;
	}
}