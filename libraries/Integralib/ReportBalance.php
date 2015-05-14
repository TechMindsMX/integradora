<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 30-Apr-15
 * Time: 9:36 AM
 */

namespace Integralib;


class ReportBalance extends \IntegradoOrders {

	public $capital;
	public $depositos;
	protected $fechaInicio;
	protected $fechaFin;
	protected $filtroProyect;
	protected $activos;
	protected $pasivos;
	protected $timoneTxs;
	protected $timoneTxsOrders;
	private $integradoId;

	function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
		$this->fechaInicio  = !is_null($fechaInicio) ? $fechaInicio : strtotime(date('01-01-Y'));
		$this->fechaFin     = !is_null($fechaFin) ? $fechaFin : strtotime(date('d-m-Y'));
		$this->filtroProyect = $proyecto;
		$this->integradoId = $integradoId;

		$this->timoneTxs = $this->setTimoneUserTxsIds();
		$this->timoneTxsOrders = $this->getOrderForTxs();

		$this->orders = $this->findOrders( $integradoId );

		$this->income = $this->getData( $this->getIncomeOrders() );
		$this->deposit = $this->getData( $this->getDepositOrders() );
		$this->withdraw = $this->getData( $this->getWithdrawalOrders() );
		$this->expense = $this->getData( $this->getExpenseOrders() );
	}

	public function calculateActivos(){
		$this->activos->ivaCompras          = $this->expense->pagado->iva;
		$this->activos->netoSaldoVentas     = $this->income->saldo->neto;

		$this->activos->banco   = $this->income->pagado->total + $this->deposit->pagado->total - $this->expense->pagado->total - $this->withdraw->pagado->total;
		$this->activos->total   = $this->activos->ivaCompras + $this->activos->netoSaldoVentas + $this->activos->banco;
	}

	public function calculatePasivos(){
		$this->pasivos->cuentasPorPagar = $this->expense->saldo->neto;
		$this->pasivos->ivaEnVentas     = $this->income->pagado->iva;

		$this->pasivos->resultado       = $this->income->nominal->neto - $this->expense->nominal->neto;

		$this->pasivos->depositos       = $this->deposit->pagado->total;
		$this->pasivos->retiros         = $this->withdraw->pagado->total;

		$this->pasivos->total           = $this->pasivos->cuentasPorPagar + $this->pasivos->ivaEnVentas + $this->pasivos->resultado + $this->pasivos->depositos - $this->pasivos->retiros;
	}

	public function calculateCapital(){
		$this->capital->total = $this->getResultadoAnterior() + $this->pasivos->resultado;
	}

	public function calculatePastExcersises() {
		$this->pasivos->ejecicioAnterior = $this->getResultadoAnterior();
		$this->depositos->ejecicioAnterior = $this->getPastDeposits();
		$this->retiros->ejecicioAnterior = $this->getPastWithdrwals();
	}

	private function getResultadoAnterior() {
		// TODO: traer el resultado anterior
		return 0;
	}

	public function getPastDeposits() {
		// TODO: traer el resultado anterior
		return 0;
	}

	private function getPastWithdrwals() {
		// TODO: traer el resultado anterior
		return 0;
	}

	public function getData($orders){
		$sumaOrdenes = OrdenFn::sumaOrders( $orders );

		return $sumaOrdenes;
	}

	public function findOrders( $integradoId = null ) {
		$cond = $this->getConditions( $integradoId );

		$db = \JFactory::getDbo();
		$types = array(
			array(
				'table' => '#__ordenes_compra',
				'type' => 'odc'
			),
			array(
				'table' => '#__ordenes_retiro',
				'type' => 'odr'
			),
			array(
				'table' => '#__ordenes_venta',
				'type' => 'odv'
			),
			array(
				'table' => '#__ordenes_deposito',
				'type' => 'odd'
			),
		);

		$result                  = new \stdClass();
		foreach ( $types as $params ) {
			$result->$params['type'] = $this->queryOrders( $db, $params, $cond );
		}

		return $result;
	}

	/**
	 * @return mixed
	 */
	public function getActivos() {
		return $this->activos;
	}

	/**
	 * @return mixed
	 */
	public function getPasivos() {
		return $this->pasivos;
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

	private function getConditions( $integradoId = null ) {
		$db = \JFactory::getDbo();
		if (isset($integradoId)) {
			$cond[] = 'integradoId = '. $db->quote($integradoId);
		}
		if ( (INT)$this->filtroProyect > 0 ) {
			$cond[] = 'proyecto = '. $db->quote($this->filtroProyect);
		}
		if (isset($this->fechaInicio) && isset($this->fechaFin)) {
			$cond[] = '((paymentDate >= '. $db->quote($this->fechaInicio) .
			          ' AND paymentDate <= '. $db->quote($this->fechaFin). ') OR ' .
			          '(createdDate >= '. $db->quote($this->fechaInicio).
			          ' AND createdDate <= '. $db->quote($this->fechaFin). '))';
		}
		if ( !isset( $cond ) ) {
			$return = ' status IN (5,8,13)';
		} else {
			$cond[] = ' status IN (5,8,13)';
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
	private function queryOrders( $db, $params, $cond ) {
		$result = null;

		$query = $db->getQuery( true )
		            ->select( '*' )
		            ->from( $db->quoteName( $params['table'] ) )
		            ->where( $cond );
		$db->setQuery( $query );

		foreach ( $db->loadAssocList() as $order ) {
			$result[ $order['id'] ] = OrderFactory::getOrder( null, $params['type'], $order);
		}

		return $result;
	}

	public function getIncomeOrders() {
		return $this->orders->odv;
	}

	public function getDepositOrders() {
		return $this->orders->odd;
	}

	public function getExpenseOrders() {
		return $this->orders->odc;
	}

	public function getWithdrawalOrders() {
		return $this->orders->odr;
	}

	public function setTimoneUserTxsIds() {
		$integ = new \IntegradoSimple($this->integradoId);
		$integ->getTimOneData();

		$req = new TimOneRequest();

		$result = $req->getUserTxs($integ->timoneData->timoneUuid);

		$timoneTxs = json_decode($result->data);

		return $timoneTxs;
	}

	private function getOrderForTxs() {
		$orders = array();
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName(array('txs.idTx', 'txs.idIntegrado', 'txs.date', 'txs.idComision', 'piv.amount', 'piv.idOrden', 'piv.orderType')))
			->from($db->quoteName('#__txs_timone_mandato', 'txs'))
			->join('left', $db->quoteName('#__txs_mandatos', 'piv').' ON (txs.id = piv.id)')
			->where(" (txs.date >= ".$db->quote($this->fechaInicio)." AND txs.date <= ".$db->quote($this->fechaFin).") AND txs.idTx IN (".$this->getArrayOfTxs().") AND piv.idOrden IS NOT NULL");
		$db->setQuery($query);

		$result = $db->loadObjectList();

		foreach ( $result as $val ) {
			$orders[] = OrderFactory::getOrder($val->idOrden, $val->orderType);
		}

		return $orders;
	}

	private function getArrayOfTxs() {
		foreach ( $this->timoneTxs as $tx ) {
			$array[] = $tx->uuid;
		}

		return "'".implode("', '", $array)."'";
	}

	private function calculateIxC() {
		$ordersFiltered = \getFromTimOne::filterOrdersByStatus($this->orders->odv, array(5));

		return OrdenFn::sumaOrders($ordersFiltered);
	}

	private function calculateDxC() {
		$ordersFiltered = \getFromTimOne::filterOrdersByStatus($this->orders->odd, array(5));

		return OrdenFn::sumaOrders($ordersFiltered);
	}

}

