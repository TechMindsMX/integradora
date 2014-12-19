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
		$report = $r->getExistingBalance();

		return $report[0];
	}

	public function generateBalance($vars) {

		$r = new ReportBalance( array('balanceId'=>null, 'integradoId' => $vars['integradoId']) );
		$report = $r->generateBalance();

		return $report[0];
	}
}