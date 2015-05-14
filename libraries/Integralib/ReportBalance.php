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
		$this->depositos->ejecicioAnterior = $this->calculatePastDeposits();
		$this->retiros->ejecicioAnterior = $this->calculatePastWithdrwals();
	}

	public function calculatePastDeposits() {
		return 0;
	}

	private function calculatePastWithdrwals() {
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
		$timoneTxs = json_decode('[{"id":51,"origin":"c2d29e28ac2746ebbb9e9936e20b2a25","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"fb43d0e19ad0495cbc1acb64a972ae7a","reference":null,"timestamp":1430252163205,"type":"TRANSFER","amount":500.00},{"id":53,"origin":"c2d29e28ac2746ebbb9e9936e20b2a25","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"fe39058d641f427fb874db113f4e36ae","reference":null,"timestamp":1430252193722,"type":"TRANSFER","amount":100.00},{"id":56,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"6489b6b69dba4fd8a6ddd84da35104c8","reference":null,"timestamp":1430257595488,"type":"TRANSFER","amount":232.00},{"id":57,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"77dba371fcc440c198cb5a27b66153c8","reference":null,"timestamp":1430257616806,"type":"TRANSFER","amount":348.00},{"id":58,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"38e04a5e3b004c7185cf9cb23365b914","reference":null,"timestamp":1430257627085,"type":"TRANSFER","amount":290.00},{"id":59,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"c8d1de44067c44f08b9705ff9fa11838","reference":null,"timestamp":1430257634139,"type":"TRANSFER","amount":174.00},{"id":67,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"3c095ff8355c4fd88a7f24be74dd8fcc","uuid":"b3bd4d9d72724126b76aaa032eaed2cb","reference":null,"timestamp":1430344301380,"type":"TRANSFER","amount":116.00},{"id":68,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"3c095ff8355c4fd88a7f24be74dd8fcc","uuid":"a2e278427e0a4945a0d807216cfe91b0","reference":null,"timestamp":1430344307593,"type":"TRANSFER","amount":232.00},{"id":69,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"932c623930004a5bb9334c60740d63e0","reference":null,"timestamp":1430344314708,"type":"CASH_OUT","amount":174.00},{"id":70,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"c7ae87742f5843b1a215afcc9805f2cc","reference":null,"timestamp":1430344324445,"type":"CASH_OUT","amount":300.00},{"id":71,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"63595b39b08b4401a028a99f5c5ca430","reference":null,"timestamp":1430344331472,"type":"CASH_OUT","amount":50.00}]');

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

	private function getResultadoAnterior() {
		// TODO: traer el resultado anterior
		return 0;
	}

}

