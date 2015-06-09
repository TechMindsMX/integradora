<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 30-Apr-15
 * Time: 9:36 AM
 */

namespace Integralib;


class ReportBalance extends ReportOrders {

	public $capital;
	public $depositos;
	protected $activos;
	protected $pasivos;

	function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
		$this->fechaInicio  = $fechaInicio ? $fechaInicio : strtotime(date('01-m-Y'));
		$this->fechaFin     = !is_null($fechaFin) ? $fechaFin : strtotime('last day of this month');
		$this->filtroProyect = $proyecto;
		$this->integradoId = $integradoId;

		$this->timoneTxs = $this->getTimoneUserTxsIds();
		$result = $this->getOrderForTxs($this->timoneTxs);

		foreach ( $result as $val ) {
			$orders[] = OrderFactory::getOrder( $val->idOrden, $val->orderType );
		}

		$this->timoneTxsOrders = $orders;

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
		$this->retiros->ejecicioAnterior = $this->calculatePastWithdrwals();
	}

	private function getResultadoAnterior() {
		// TODO: traer el resultado anterior
		return 0;
	}

	public function getPastDeposits() {
		// TODO: traer el resultado anterior
		return 0;
	}

	private function calculatePastWithdrwals() {
		// TODO: traer el resultado anterior
		return 0;
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

	protected function getConditions( $integradoId = null ) {
		$db = \JFactory::getDbo();
		if ( isset( $integradoId ) ) {
			$cond[] = 'integradoId = ' . $db->quote( $integradoId );
		}
		if ( (INT) $this->filtroProyect > 0 ) {
			$cond[] = 'proyecto = ' . $db->quote( $this->filtroProyect );
		}
		if ( isset( $this->fechaInicio ) && isset( $this->fechaFin ) ) {
			$cond[] = '((paymentDate >= ' . $db->quote( $this->fechaInicio ) .
			          ' AND paymentDate <= ' . $db->quote( $this->fechaFin ) . ') OR ' .
			          '(createdDate >= ' . $db->quote( $this->fechaInicio ) .
			          ' AND createdDate <= ' . $db->quote( $this->fechaFin ) . '))';
		}
		if ( ! isset( $cond ) ) {
			$return = ' status IN (5,8,13)';
		} else {
			$cond[] = ' status IN (5,8,13)';
			$return = implode( ' AND ', $cond );
		}

		return $return;
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

