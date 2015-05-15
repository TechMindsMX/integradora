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
		$this->fechaInicio  = $fechaInicio;
		$this->fechaFin     = $fechaFin;
		$this->filtroProyect = $proyecto;
		$this->integradoId = $integradoId;

		$this->integrado = new \IntegradoSimple($this->integradoId);
		$this->integrado->getTimOneData();

		$this->timoneTxs = $this->getTimoneUserTxsIds();

		$expenseTxs = $this->getExpenseTxs();
		$incomeTxs = $this->getIncomeTxs();

		$expenseOrders    = $this->getOrderForTxs($expenseTxs);
		foreach ( $expenseTxs as $val ) {
			$order = OrderFactory::getOrder( $expenseOrders[$val->uuid]->idOrden, $expenseOrders[$val->uuid]->orderType );
			$val->order = $order;
		}

		$this->expenseOrders = $expenseTxs;


		$this->inomeOrders      = $this->getOdvs( $this->getOrderForTxs($incomeTxs) );

		$this->txs = $this->findTxsAndOrders( $integradoId );

	}

	public function findTxsAndOrders( $integradoId = null ) {
		$cond = $this->getConditions( $integradoId, 'txs' );

		$db = \JFactory::getDbo();
		$types = array(
			array(
				'table' => '#__ordenes_compra',
				'type' => 'odc'
			),
			array(
				'table' => '#__ordenes_venta',
				'type' => 'odv'
			),
			array(
				'table' => '#__ordenes_deposito',
				'type' => 'odd'
			),
			array(
				'table' => '#__ordenes_retiro',
				'type' => 'odr'
			),
		);

		$result = $this->queryTxs( $db, $cond );

		$result = $this->groupByOrderType($result);

		return $result;
	}

	public function calculateIngresos(){
		$this->ingresos = $this->getData( $this->getIncomeTxs() );
	}

	public function calculateEgresos(){
		$this->egresos = $this->getData( $this->getExpenseTxs() );
	}

	public function calculateDepositos(){
		$this->depositos = $this->getData( $this->getDepositTxs() );
	}

	public function calculateRetiros(){
		$this->retiros = $this->getData( $this->getWithdrawTxs() );
	}

	public function getData( $txs ){
		$sumaOrders = new \stdClass();

		if ( ! empty( $txs ) ) {
			foreach ( $txs as $key => $tx ) {
				$sumaOrders->pagado->total  += $tx->getTotalAmount();
				$ivaOrderRate = ( $tx->order->iva / $tx->order->subTotalAmount );
				$sumaOrders->pagado->net = $tx->getTotalAmount() / (1+$ivaOrderRate);
				$sumaOrders->pagado->iva = $sumaOrders->pagado->net * $ivaOrderRate;
			}
		}

		return $sumaOrders;
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
		return array_filter($this->timoneTxs, array($this, 'filterIncomeTxs' ) );
	}

	public function filterIncomeTxs($val) {
		return $val->destination == $this->integrado->timoneData->timoneUuid && $val->type !== 'CASH_OUT';
	}

	public function getDepositTxs() {
		return $this->txs->odd;
	}

	public function getExpenseTxs() {
		return array_filter($this->timoneTxs, array($this, 'filterExpenseTxs' ) );
	}

	public function filterExpenseTxs($val) {
		return $val->origin == $this->integrado->timoneData->timoneUuid;
	}

	public function getWithdrawTxs() {
		return $this->txs->odr;
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

	private function getOdvs( $getOrderForTxs ) {
		foreach ( $getOrderForTxs as $key => $order ) {
			if ( is_a($order, 'Integralib\OdCompra') ) {
				$getOrderForTxs[$key] = OrderFactory::getOrder( OrdenFn::getRelatedOdvIdFromOdcId($order->getId()), 'odv') ;
			}
		}

		return $getOrderForTxs;
	}

}
