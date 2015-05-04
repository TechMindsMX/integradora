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
	protected $activos;
	protected $pasivos;
	private $integradoId;

	function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
		$this->fechaInicio  = $fechaInicio;
		$this->fechaFin     = $fechaFin;
		$this->filtroProyect = $proyecto;
		$this->integradoId = $integradoId;

		$this->setTimoneUserTxs();
		$this->getOrderForTxs();

		$this->orders = $this->findOrders( $integradoId );
	}

	public function calculateActivos(){
		$this->activos->banco = $this->getData( $this->getIncomeOrders() )->total - $this->getData( $this->getExpenseOrders() )->total;
	}

	public function calculatePasivos(){
		$this->pasivos = $this->getData( $this->getExpenseOrders() );
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

	public function getExpenseOrders() {
		return $this->orders->odc;
	}

	public function setTimoneUserTxs() {
		$integ = new \IntegradoSimple($this->integradoId);
		$integ->getTimOneData();

		$req = new TimOneRequest();

		$result = $req->getUserTxs($integ->timoneData->timoneUuid);

		$this->timoneTxs = json_decode($result->data);
		$this->timoneTxs = json_decode('[{"id":51,"origin":"c2d29e28ac2746ebbb9e9936e20b2a25","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"fb43d0e19ad0495cbc1acb64a972ae7a","reference":null,"timestamp":1430252163205,"type":"TRANSFER","amount":500.00},{"id":53,"origin":"c2d29e28ac2746ebbb9e9936e20b2a25","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"fe39058d641f427fb874db113f4e36ae","reference":null,"timestamp":1430252193722,"type":"TRANSFER","amount":100.00},{"id":56,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"6489b6b69dba4fd8a6ddd84da35104c8","reference":null,"timestamp":1430257595488,"type":"TRANSFER","amount":232.00},{"id":57,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"77dba371fcc440c198cb5a27b66153c8","reference":null,"timestamp":1430257616806,"type":"TRANSFER","amount":348.00},{"id":58,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"38e04a5e3b004c7185cf9cb23365b914","reference":null,"timestamp":1430257627085,"type":"TRANSFER","amount":290.00},{"id":59,"origin":"3c095ff8355c4fd88a7f24be74dd8fcc","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"c8d1de44067c44f08b9705ff9fa11838","reference":null,"timestamp":1430257634139,"type":"TRANSFER","amount":174.00},{"id":67,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"3c095ff8355c4fd88a7f24be74dd8fcc","uuid":"b3bd4d9d72724126b76aaa032eaed2cb","reference":null,"timestamp":1430344301380,"type":"TRANSFER","amount":116.00},{"id":68,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"3c095ff8355c4fd88a7f24be74dd8fcc","uuid":"a2e278427e0a4945a0d807216cfe91b0","reference":null,"timestamp":1430344307593,"type":"TRANSFER","amount":232.00},{"id":69,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"932c623930004a5bb9334c60740d63e0","reference":null,"timestamp":1430344314708,"type":"CASH_OUT","amount":174.00},{"id":70,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"c7ae87742f5843b1a215afcc9805f2cc","reference":null,"timestamp":1430344324445,"type":"CASH_OUT","amount":300.00},{"id":71,"origin":"38d021ada4134c34ba5b66dba9665482","destination":"38d021ada4134c34ba5b66dba9665482","uuid":"63595b39b08b4401a028a99f5c5ca430","reference":null,"timestamp":1430344331472,"type":"CASH_OUT","amount":50.00}]');
	}

	private function getOrderForTxs() {
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName(array('txs.idTx', 'txs.idIntegrado', 'txs.date', 'txs.idComision', 'piv.amount', 'piv.idOrden', 'piv.orderType')))
			->from($db->quoteName('#__txs_timone_mandato', 'txs'))
			->join('left', $db->quoteName('#__txs_mandatos', 'piv').' ON ((txs.id = piv.id))')
			->where(" ((txs.date >= '1427846400' AND txs.date <= '1430352000') OR (txs.date <= '1430352000' AND txs.date <= '1430352000')) AND txs.idTx = '6489b6b69dba4fd8a6ddd84da35104c8' ");
		$db->setQuery($query);

		$result = $db->loadObjectList();
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

