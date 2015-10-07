<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 10:38 AM
 */

namespace Integralib;

defined('_JEXEC') or die('Restricted access');

class ReportFlujo extends ReportOrders {

	protected $fechaInicio;
	protected $fechaFin;
	protected $filtroProyect;
	protected $ingresos;
	protected $egresos;
	protected $depositos;

	protected $retiros;

	function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
		$this->fechaInicio  = strtotime($fechaInicio);
		$this->fechaFin     = strtotime($fechaFin);
		$this->filtroProyect = $proyecto;
		$this->integradoId = $integradoId;

		$this->integrado = new \IntegradoSimple($this->integradoId);
		$this->integrado->getTimOneData();

		$this->timoneTxs = $this->getTimoneUserTxsIds();

		if (!empty($this->timoneTxs) ) {
			$this->calculateExpenses();

			$this->calculateIncome();

			$this->calculateIngresos();
			$this->calculateEgresos();
			$this->calculateDepositos();
			$this->calculateRetiros();
		}

	}

	public function calculateIngresos(){
		$txs = $this->getTxs('Integralib\OdVenta');

		$this->ingresos = $this->sumTxs( $txs );
	}

	public function calculateEgresos(){
		$txs = $this->getTxs('Integralib\OdCompra');

		$this->egresos = $this->sumTxs( $txs );
	}

	public function calculateDepositos(){
		$txs = $this->getTxs('Integralib\OdDeposito');

		$this->depositos = $this->sumTxs( $txs );
	}

	public function calculateRetiros(){
		$txs = $this->getTxs('Integralib\OdRetiro');

		$this->retiros = $this->sumTxs( $txs );
	}

	/**
	 * @return mixed
	 */
	public function getIngresos() {
		return $this->ingresos;
	}

	/**
	 * @return mixed
	 */
	public function getEgresos() {
		return $this->egresos;
	}

	/**
	 * @return mixed
	 */
	public function getDepositos() {
		return $this->depositos;
	}

	/**
	 * @return mixed
	 */
	public function getRetiros() {
		return $this->retiros;
	}

	/**
	 * @return mixed
	 */
	public function getOrders() {
		return $this->orders;
	}

	/**
	 * @return mixed
	 */
	public function getFechaFin()
	{
		return $this->fechaFin;
	}

	/**
	 * @return mixed
	 */
	public function getFechaInicio()
	{
		return $this->fechaInicio;
	}

	protected function getConditions( $integradoId = null, $type = '' ) {
		$db = \JFactory::getDbo();
		if (isset($integradoId)) {
			$cond[] = 'idIntegrado = '. $db->quote($integradoId);
		}
		if ( (INT)$this->filtroProyect > 0 ) {
			$cond[] = 'proyecto = '. $db->quote($this->filtroProyect);
		}
		if (isset($this->fechaInicio) && isset($this->fechaFin) && $type == '' ) {
			$cond[] = ' ((' . $type . 'paymentDate >= ' . $db->quote( $this->getTimestamp( $this->fechaInicio ) ) .
			          ' AND ' . $type . 'paymentDate <= ' . $db->quote( $this->getTimestamp( $this->fechaFin ) ) . ') OR ' .
			          ' (' . $type . 'createdDate <= ' . $db->quote( $this->getTimestamp( $this->fechaFin ) ) .
			          ' AND ' . $type . 'createdDate <= ' . $db->quote( $this->getTimestamp( $this->fechaFin ) ) . '))';
		}
		if (isset($this->fechaInicio) && isset($this->fechaFin) && $type == 'txs') {
			$cond[] = ' ((txs.date >= '. $db->quote( $this->getTimestamp($this->fechaInicio) ) .
			          ' AND txs.date <= '. $db->quote( $this->getTimestamp($this->fechaFin) ) . ') OR ' .
			          ' (txs.date <= '. $db->quote( $this->getTimestamp($this->fechaFin ) ) .
			          ' AND txs.date <= '. $db->quote( $this->getTimestamp($this->fechaFin) ) . '))';
		}
		if ( !isset( $cond ) && $type == '' ) {
			$return = ' status IN (5,8,13)';
		} else {
			$return = implode(' AND ', $cond);
		}

		return $return;
	}

	/**
	 * @return mixed
	 */
	public function calculateExpenses() {
		$expenseTxs = $this->getExpenseTxs();

		$expenseOrders = $this->getOrderForTxs( $expenseTxs );
		foreach ( $expenseTxs as $val ) {
			if (isset($expenseOrders[$val->uuid]->idOrden)) {
				$val->order = OrderFactory::getOrder( $expenseOrders[ $val->uuid ]->idOrden,
                                                      $expenseOrders[ $val->uuid ]->orderType );
			}
			if ( isset( $val->order ) ) {
				$val->order->setTxsByUuid();
			}
		}

		$this->expenseTxs = $expenseTxs;
	}

	public function calculateIncome() {
		$incomeTxs = $this->getIncomeTxs();

		$orders = $this->getOrderForTxs( $incomeTxs );
		foreach ( $incomeTxs as $val ) {
			if (isset($orders[$val->uuid]->idOrden)) {
				$order      = OrderFactory::getOrder( $orders[ $val->uuid ]->idOrden, $orders[ $val->uuid ]->orderType );
			}
			$val->order = $this->getOdvFromOdc( $order );
			if ( isset( $val->order ) ) {
				$val->order->setTxsByUuid();
			}
		}

		$this->incomeTxs = $incomeTxs;
	}

	/**
	 * @param $txs
	 *
	 * @return \stdClass
	 */
	public function sumTxs( $txs ) {
		$obj = new \stdClass();
		foreach ( $txs as $k => $tx ) {
			$obj->net += $tx->order->txs[ $k ]->detalleTx->net;
			$obj->iva += $tx->order->txs[ $k ]->detalleTx->iva;
			$obj->amount += $tx->order->txs[ $k ]->detalleTx->amount;
		}

		return $obj;
	}

	/**
	 * @param $db
	 * @param $params
	 * @param $cond
	 *
	 * @return null
	 * @internal param $result
	 */
	protected function queryOrders( $db, $params, $cond ) {
		$result = null;

		$query = $db->getQuery( true )
		            ->select( '*' )
		            ->from( $db->quoteName( $params['table'] ) )
		            ->where( $cond );
		$db->setQuery( $query );

		foreach ( $db->loadAssocList() as $order ) {
			$result[ $order['id'] ] = OrderFactory::getOrder( $order, $params );
		}

		return $result;
	}

	private function queryTxs( $db, $cond ) {
		$result = null;

		$query = $db->getQuery( true )
		            ->select( '*' )
		            ->from( $db->quoteName( '#__txs_timone_mandato', 'txs' ) )
		            ->join( 'LEFT', $db->quoteName( '#__txs_mandatos', 'piv' ) . ' ON ( txs.id = piv.id )' )
		            ->where( $cond );
		$db->setQuery( $query );
		$result = $db->loadObjectList('id', '\Integralib\Tx');

		foreach ( $result as $tx ) {
			$tx->order = OrderFactory::getOrder( $tx->idOrden, $tx->orderType);
		}

		return array_filter($result);
	}

	public function getIncomeTxs() {
		$uuid = $this->integrado->timoneData->timoneUuid;
		return array_filter($this->timoneTxs, function ($val) use ($uuid) {
			return $val->destination == $uuid && $val->type !== 'CASH_OUT';
		});
	}

	public function getDepositTxs() {
		return $this->txs->odd;
	}

	public function getExpenseTxs() {
		$uuid = $this->integrado->timoneData->timoneUuid;
		return array_filter($this->timoneTxs, function ($val) use ($uuid) {
			return $val->origin == $uuid;
		});
	}

	public function getWithdrawTxs() {
		$uuid = $this->integrado->timoneData->timoneUuid;
		return array_filter($this->timoneTxs, function ($val) use ($uuid) {
			return $val->origin == $uuid && $val->destination == $uuid && $val->type == 'CASH_OUT';
		});
	}

	private function getTimestamp( $fecha ) {
		$dateTime = new \DateTime($fecha);
		return $dateTime->format('U');
	}

	private function groupByOrderType( $result ) {
		$txs = new \stdClass();

		foreach ( $result as $key => $val ) {
			$ot = $val->orderType;
			$txs->$ot->$key = $val;
		}

		return $txs;
	}

	private function getOdvFromOdc( $order ) {
		if ( is_a($order, 'Integralib\OdCompra') ) {
			$order = OrderFactory::getOrder( OrdenFn::getRelatedOdvIdFromOdcId($order->getId()), 'odv') ;
		}

		return $order;
	}

	/**
	 * @param string $objName
	 *
	 * @return array
	 */
	public function getTxs( $objName ) {
		return array_filter(array_merge($this->expenseTxs, $this->incomeTxs), function($tx) use ($objName) {
			return is_a($tx->order, $objName);
		});
	}

}
