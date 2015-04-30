<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 30-Apr-15
 * Time: 9:36 AM
 */

namespace Integralib;


class ReportBalance extends \IntegradoOrders {

	protected $fechaInicio;
	protected $fechaFin;
	protected $filtroProyect;
	protected $ingresos;
	protected $egresos;

	function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
		$this->fechaInicio  = $fechaInicio;
		$this->fechaFin     = $fechaFin;
		$this->filtroProyect = $proyecto;

		$this->orders = $this->findOrders( $integradoId );
	}

	public function calculateIngresos(){
		$this->ingresos = $this->getData( $this->getIncomeOrders() );
	}

	public function calculateEgresos(){
		$this->egresos = $this->getData( $this->getExpenseOrders() );
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
				'table' => '#__ordenes_venta',
				'type' => 'odv'
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

	public function getExpenseOrders() {
		return $this->orders->odc;
	}

}


//class ReportBalance extends \IntegradoOrders {
//	public $integradoId;
//	public $retiros;
//	public $depositos;
//	public $capital;
//	public $pasivo;
//	public $observaciones;
//	public $status;
//	public $paymentType;
//	public $currency;
//	public $createdDate;
//	public $proyectId;
//	public $numBalance;
//	public $id;
//	public $period;
//	public $year;
//	public $activo;
//	protected $request;
//
//	/**
//	 * @param $params array(integradoId => $integradoId, balanceId  => $balanceId = null)
//	 */
//	function __construct( $params ) {
//		list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin();
//
//		$this->request->integradoId = $params['integradoId'];
//
//		if ( isset( $params['balanceId'] ) ) {
//			if ( $params['balanceId'] != 0 ) {
//				$this->request->balanceId   = $params['balanceId'];
//			}
//		}
//
//		parent::__construct($params['integradoId']);
//	}
//
//	public function generateBalance( ) {
//		$respuesta = null;
//
//		$this->createData();
//		getFromTimOne::convierteFechas( $this );
//		$this->setDatesForDisplay();
//
//	}
//
//	/**
//	 * @internal param $b
//	 */
//	public function createData() {
//		$this->orders = $this->findOrders( $this->request->integradoId );
//
//	}
//
//	public function findOrders( $integradoId = null ) {
//		$cond = $this->getConditions( $integradoId );
//
//		$db = \JFactory::getDbo();
//		$types = array(
//			array(
//				'table' => '#__ordenes_compra',
//				'type' => 'odc'
//			),
//			array(
//				'table' => '#__ordenes_venta',
//				'type' => 'odv'
//			),
//		);
//
//		$result                  = new \stdClass();
//		foreach ( $types as $params ) {
//			$result->$params['type'] = $this->queryOrders( $db, $params, $cond );
//		}
//
//		return $result;
//	}
//
//	private function getConditions( $integradoId = null ) {
//		$db = \JFactory::getDbo();
//		if (isset($integradoId)) {
//			$cond[] = 'integradoId = '. $db->quote($integradoId);
//		}
//		if ( (INT)$this->filtroProyect > 0 ) {
//			$cond[] = 'proyecto = '. $db->quote($this->filtroProyect);
//		}
//		if (isset($this->fechaInicio) && isset($this->fechaFin)) {
//			$cond[] = '((paymentDate >= '. $db->quote($this->fechaInicio) .
//			          ' AND paymentDate <= '. $db->quote($this->fechaFin). ') OR ' .
//			          '(createdDate >= '. $db->quote($this->fechaInicio).
//			          ' AND createdDate <= '. $db->quote($this->fechaFin). '))';
//		}
//		if ( !isset( $cond ) ) {
//			$return = ' status IN (5,8,13)';
//		} else {
//			$cond[] = ' status IN (5,8,13)';
//			$return = implode(' AND ', $cond);
//		}
//
//		return $return;
//	}
//
//	private function queryOrders( $db, $params, $cond ) {
//		$result = null;
//
//		$query = $db->getQuery( true )
//		            ->select( '*' )
//		            ->from( $db->quoteName( $params['table'] ) )
//		            ->where( $cond );
//		$db->setQuery( $query );
//
//		foreach ( $db->loadAssocList() as $order ) {
//			$result[ $order['id'] ] = OrderFactory::getOrder( null, $params['type'], $order);
//		}
//
//		return $result;
//	}
//
//	private function getIvaVentasPeriodo( ) {
//		$ivas = array();
//		$invoices   = getFromTimOne::getOrdersCxC($this->request->integradoId);
//
//		$filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);
//
//		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
//		foreach ( $filteredOrders as $fact ) {
//			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);
//
//			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
//			if ( $testStatus && $testDates) {
//				$ivas[] = $fact->iva;
//			}
//		}
//
//		return array_sum($ivas);
//	}
//
//
//	private function getIvaComprasPeriodo() {
//		$ivas = array();
//		$invoices   = getFromTimOne::getOrdersCxP($this->request->integradoId);
//
//		$filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);
//
//		if ( ! empty( $filteredOrders ) ) {
//			$respuesta = $this->sumOrders($filteredOrders);
//		}
////		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
////		foreach ( $filteredOrders as $fact ) {
////			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);
////
////			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
////			if ( $testStatus && $testDates) {
////				$ivas[] = $fact->iva;
////			}
////		}
//
//		return array_sum($ivas);
//	}
//
//	private function getCxP() {
//		return $this->pasivo->data = $this->getData($this->orders->odc);
//	}
//
//	private function getCxC() {
//		return $this->activo->data = $this->getData($this->orders->odv);
//	}
//
//	public function getData($Orders){
//		$ordenesFiltradas = getFromTimOne::filterOrdersByStatus($Orders,array(5,8,13));
//		$sumaOrdenes = OrdenFn::sumaOrders( $ordenesFiltradas );
//
//		return $sumaOrdenes;
//	}
//
//	private function setDatesForDisplay() {
//		$this->period->startDate   = date('d-m-Y', $this->period->startDate->timestamp);
//		$this->period->endDate     = date('d-m-Y', $this->period->endDate->timestamp);
//	}
//
//	/**
//	 * @param null $year
//	 *
//	 * @return array
//	 */
//	public function setDatesInicioFin( $year = null ) {
//		$inicio = 'first day of January';
//		$final = 'first day of this month';
//		if (isset($year)) {
//			$inicio = 'first day of January '.$year;
//			$nextYear = (int)$year+1;
//			$final = 'first day of January '.$nextYear;
//		}
//		$timeZone    = new \DateTimeZone( 'America/Mexico_City' );
//		$fechaInicio = new \DateTime( $inicio, $timeZone );
//		$fechaFin    = new \DateTime( $final, $timeZone );
//		$fechaFin->setTime( 0, 0, 0 );
//		$fechaInicio->timestamp = $fechaInicio->getTimestamp();
//		$fechaFin->timestamp    = $fechaFin->getTimestamp();
//
//		return array ( $fechaInicio, $fechaFin );
//	}
//
//	private function getBancoSaldoEndDate() {
//		// TODO: Operar el saldo con las Tx para sacar el saldo a cirre de periodo del balance
//		return (float)946;
//	}
//
//	private function sumOrders( $orders ) {
//		return OrdenFn::sumaOrders( $orders );
//	}
//
//	public static function getIntegradoExistingBalanceList($integradoId) {
//		$data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$integradoId );
//
//		return $data;
//	}
//
//	public function getExistingBalance() {
//
//		if ( ! empty( $this->request->integradoId ) && ! empty($this->request->balanceId) ) {
//			$data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$this->request->integradoId.' AND id = '. $this->request->balanceId );
//			list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin($data[0]->year);
//		}
//
//		$this->generateBalance();
//	}
//}

