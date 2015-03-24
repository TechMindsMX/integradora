<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelSolicitudliquidacion extends JModelItem
{
    protected $dataModelo;
	protected $integradoId;

	/**
	 * @param mixed $integradoId
	 */
	public function setIntegradoId( $integradoId ) {
		$this->integradoId = $integradoId;
	}

	public function getIntegrado() {
		$integ = new IntegradoSimple($this->integradoId);

		$integ->getTimOneData();

		return $integ;
	}

    public function getSaldoOperaciones($operaciones){
		return getFromTimOne::getSaldoOperacionesPorLiquidar($operaciones);
    }

    public function getOperaciones(){
	    return getFromTimOne::getOperacionesPorLiquidar($this->integradoId);
    }

	public function getBalanceTxsLiquidacion() {
		$txs = $this->getTxsLiquidacion();

		$balance = 0;
		foreach ( $txs as $tx ) {
			$balance += $tx->getAmount();
		}

		return $balance;
	}

	private function getTxsLiquidacion() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from('#__txs_liquidacion_saldo')
			->where($db->quoteName('integradoId'). ' = '. $db->quote($this->integradoId));
		$db->setQuery($query);

		$txs = $db->loadObjectList('id', '\Integralib\TxLiquidacion');

		return $txs;
	}

	public function balanceLiquidable( ){

		$saldo = $this->getSaldoOperaciones($this->getOperaciones());

		return $saldo->subtotalTotalOperaciones - $this->getBalanceTxsLiquidacion();
	}
}

