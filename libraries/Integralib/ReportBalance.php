<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 30-Apr-15
 * Time: 9:36 AM
 */

namespace Integralib;


use getFromTimOne;

class ReportBalance extends \IntegradoOrders {
	public $integradoId;
	public $retiros;
	public $depositos;
	public $capital;
	public $pasivo;
	public $observaciones;
	public $status;
	public $paymentType;
	public $currency;
	public $createdDate;
	public $proyectId;
	public $numBalance;
	public $id;
	public $period;
	public $year;
	public $activo;
	protected $request;

	/**
	 * @param $params array(integradoId => $integradoId, balanceId  => $balanceId = null)
	 */
	function __construct( $params ) {
		list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin();

		$this->request->integradoId = $params['integradoId'];

		if ( isset( $params['balanceId'] ) ) {
			if ( $params['balanceId'] != 0 ) {
				$this->request->balanceId   = $params['balanceId'];
			}
		}

		parent::__construct($params['integradoId']);
	}

	public function generateBalance( ) {
		$respuesta = null;

		$this->createData();
		getFromTimOne::convierteFechas( $this );
		$this->setDatesForDisplay();

	}

	public function getExistingBalance() {

		if ( ! empty( $this->request->integradoId ) && ! empty($this->request->balanceId) ) {
			$data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$this->request->integradoId.' AND id = '. $this->request->balanceId );
			list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin($data[0]->year);
		}

		$this->generateBalance();
	}

	/**
	 * @internal param $b
	 */
	public function createData() {
		// TODO: quitar simulados
		$this->createdDate                     = time();
		$this->year                            = 2013;
		$this->pasivo->cuentasPorPagar         = $this->getCxP()->neto;; // suma historica de CxP
		$this->pasivo->ivaVentas               = $this->getCxP()->iva;
		$this->pasivo->total                   = $this->pasivo->cuentasPorPagar + $this->pasivo->ivaVentas;
		$this->activo->bancoSaldoEndDate       = $this->getBancoSaldoEndDate();
		$this->activo->cuentasPorCobrar        = $this->getCxC()->neto;
		$this->activo->ivaCompras              = $this->getCxC()->iva;
		$this->activo->total                   = $this->activo->cuentasPorCobrar + $this->activo->ivaCompras + $this->activo->bancoSaldoEndDate;
		$this->capital->ejecicioAnterior       = 0;
		$this->capital->totalEdoResultados     = 750;
		$this->depositos->ejecicioAnterior     = 0;
		$this->depositos->actual               = 600;
		$this->retiros->ejecicioAnterior       = 0;
		$this->retiros->actual                 = 350;

		$this->capital->total                  = ($this->capital->ejecicioAnterior + $this->capital->totalEdoResultados + $this->depositos->ejecicioAnterior + $this->depositos->actual) - ($this->retiros->ejecicioAnterior + $this->retiros->actual);
	}

	private function getIvaVentasPeriodo( ) {
		$ivas = array();
		$invoices   = getFromTimOne::getOrdersCxC($this->request->integradoId);

		$filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
		foreach ( $filteredOrders as $fact ) {
			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);

			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
			if ( $testStatus && $testDates) {
				$ivas[] = $fact->iva;
			}
		}

		return array_sum($ivas);
	}

	private function getIvaComprasPeriodo() {
		$ivas = array();
		$invoices   = getFromTimOne::getOrdersCxP($this->request->integradoId);

		$filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

		if ( ! empty( $filteredOrders ) ) {
			$respuesta = $this->sumOrders($filteredOrders);
		}
//		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
//		foreach ( $filteredOrders as $fact ) {
//			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);
//
//			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
//			if ( $testStatus && $testDates) {
//				$ivas[] = $fact->iva;
//			}
//		}

		return array_sum($ivas);
	}

	private function getCxP() {
		return $this->pasivo->data = $this->getData($this->orders->odc);
	}

	private function getCxC() {
		return $this->activo->data = $this->getData($this->orders->odv);
	}

	public function getData($Orders){
		$ordenesFiltradas = getFromTimOne::filterOrdersByStatus($Orders,array(5,8,13));
		$sumaOrdenes = OrdenFn::sumaOrders( $ordenesFiltradas );

		return $sumaOrdenes;
	}

	/**
	 * @param null $year
	 *
	 * @return array
	 */
	public function setDatesInicioFin( $year = null ) {
		$inicio = 'first day of January';
		$final = 'first day of this month';
		if (isset($year)) {
			$inicio = 'first day of January '.$year;
			$nextYear = (int)$year+1;
			$final = 'first day of January '.$nextYear;
		}
		$timeZone    = new \DateTimeZone( 'America/Mexico_City' );
		$fechaInicio = new \DateTime( $inicio, $timeZone );
		$fechaFin    = new \DateTime( $final, $timeZone );
		$fechaFin->setTime( 0, 0, 0 );
		$fechaInicio->timestamp = $fechaInicio->getTimestamp();
		$fechaFin->timestamp    = $fechaFin->getTimestamp();

		return array ( $fechaInicio, $fechaFin );
	}

	private function setDatesForDisplay() {
		$this->period->startDate   = date('d-m-Y', $this->period->startDate->timestamp);
		$this->period->endDate     = date('d-m-Y', $this->period->endDate->timestamp);
	}

	private function getBancoSaldoEndDate() {
		// TODO: Operar el saldo con las Tx para sacar el saldo a cirre de periodo del balance
		return (float)946;
	}

	private function sumOrders( $orders ) {
		return OrdenFn::sumaOrders( $orders );
	}

	public static function getIntegradoExistingBalanceList($integradoId) {
		$data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$integradoId );

		return $data;
	}
}
