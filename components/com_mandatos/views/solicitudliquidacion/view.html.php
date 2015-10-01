<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewSolicitudliquidacion extends JViewLegacy {

	public $integrado;
	protected $integradoId;
	protected $operaciones;
	protected $saldo;

	/**
	 * @param null $tpl
	 * @return bool
     */
	function display($tpl = null){
		$this->loadHelper('Mandatos');
		$model = $this->getModel();

		$session            				   = JFactory::getSession();
		$this->integradoId  				   = $session->get( 'integradoId', null, 'integrado' );
		$model->setIntegradoid($this->integradoId);
		$this->integrado    				   = $model->getIntegrado();
		$this->integrado->blockedBalance	   = MandatosHelper::getBlockedBalance();
		$this->integrado->balance       	   = MandatosHelper::getBalance($this->integradoId);
		$this->operaciones  				   = $model->getOperaciones();
		$this->saldo 						   = $model->getSaldoOperaciones($this->operaciones);
		$this->saldo->subtotalTotalOperaciones = $model->balanceLiquidable();

		$this->loadHelper('Mandatos');

		foreach ($this->operaciones as $key => $value) {
			$value->beneficiary = MandatosHelper::getClientsFromID($value->clientId, $this->integradoId);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}

		parent::display($tpl);
	}
}