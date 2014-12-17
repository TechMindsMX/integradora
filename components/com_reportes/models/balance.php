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
	protected $input;

	function __construct() {
		$this->input = JFactory::getApplication()->input;

		parent::__construct();
	}

	/**
	 * @return mixed
	 */
	private function getCancelUrl() {
		return 'index.php?option=com_reportes&view=reporteslistados&integradoId='.$this->input->get('integradoId', null, 'INT');
	}

	public function getBalance() {
		$vars = $this->input->getArray(array('id' => 'INT', 'integradoId' => 'INT'));
		$sesion = JFactory::getSession();

		// verifica el token
		$sesion->checkToken('get') or JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_ERROR'), 'error');

		$r = new ReportBalance( array('balanceId'=>$vars['id'], 'integradoId' => $vars['integradoId']) );
		$report = $r->getBalances();

		if (is_null($report) ) {
			JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_REPORT_NOT_FOUND'), 'error');
		}

		return $report[0];
	}
}