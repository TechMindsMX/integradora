<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelBalance extends JModelItem {


	function __construct( $app ) {
		$this->app = JFactory::getApplication();

		parent::__construct();
	}

	public function getBalance() {
		$balanceId = $this->app->input->get('balanceId', 'INT');
		$sesion = JFactory::getSession();

		// verifica el token
		$sesion->checkToken('get') or $this->exitWithRedirect('LBL_ERROR', 'error');



	}

	private function exitWithRedirect( $string, $string1 ) {
		var_dump($this);

	}
}