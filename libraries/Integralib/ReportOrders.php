<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 14-May-15
 * Time: 3:13 PM
 */

namespace Integralib;

abstract class ReportOrders {

	public $orders;
	protected $fechaInicio;
	protected $fechaFin;
	protected $filtroProyect;
	protected $integradoId;
	protected $timoneTxs;
	protected $timoneTxsOrders;

	public function findOrders( $integradoId = null ) {
		$cond = $this->getConditions( $integradoId );

		$db    = \JFactory::getDbo();
		$types = array (
			array (
				'table' => '#__ordenes_compra',
				'type'  => 'odc'
			),
			array (
				'table' => '#__ordenes_retiro',
				'type'  => 'odr'
			),
			array (
				'table' => '#__ordenes_venta',
				'type'  => 'odv'
			),
			array (
				'table' => '#__ordenes_deposito',
				'type'  => 'odd'
			),
		);

		$result = new \stdClass();
		foreach ( $types as $params ) {
			$result->$params['type'] = $this->queryOrders( $db, $params, $cond );
		}

		return $result;
	}

	/**
	 * @return mixed
	 */
	public function getFechaFin() {
		return $this->fechaFin;
	}

	/**
	 * @return mixed
	 */
	public function getFechaInicio() {
		return $this->fechaInicio;
	}

	abstract protected function getConditions( $integradoId = null );

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
			$result[ $order['id'] ] = OrderFactory::getOrder( null, $params['type'], $order );
		}

		return $result;
	}

	public function getData( $orders ) {
		$sumaOrdenes = OrdenFn::sumaOrders( $orders );

		return $sumaOrdenes;
	}

	/**
	 * @return array
	 */
	public function getTimoneUserTxsIds() {
		$integ = new \IntegradoSimple( $this->integradoId );
		$integ->getTimOneData();

		$req = new TimOneRequest();

		$result = $req->getUserTxs( $integ->timoneData->timoneUuid );

		$tmp = json_decode( $result->data );

		$timoneTxs = array();
		foreach ( $tmp as $tx ) {
            $fechaInicio = $this->getTimestamp($this->fechaInicio);
            $fechaFin = $this->getTimestamp($this->fechaFin);
            $txTimestamp = $tx->timestamp/1000;

			if ($txTimestamp >= $fechaInicio  && $txTimestamp <= $fechaFin) {
				$timoneTxs[$tx->uuid] = $tx;
			}
		}

		return $timoneTxs;
	}

	protected function getOrderForTxs($txs) {
		$db     = \JFactory::getDbo();

		$query = $db->getQuery( true )
		            ->select( $db->quoteName( array (
			                                      'txs.idTx',
			                                      'txs.integradoId',
			                                      'txs.date',
			                                      'txs.idComision',
			                                      'piv.amount',
			                                      'piv.idOrden',
			                                      'piv.orderType'
		                                      ) ) )
		            ->from( $db->quoteName( '#__txs_timone_mandato', 'txs' ) )
		            ->join( 'left', $db->quoteName( '#__txs_mandatos', 'piv' ) . ' ON (txs.id = piv.id)' )
		            ->where( "txs.idTx IN (" . $this->prepareArray($txs) . ")" );
		$db->setQuery( $query );

		$result = $db->loadObjectList('idTx');

		return $result;
	}

	protected function prepareArray($txs) {
		foreach ( $txs as $tx ) {
			$array[] = $tx->uuid;
		}

		return "'" . implode( "', '", $array ) . "'";
	}

	private function getTimestamp( $fecha ) {
		$tzone = \JFactory::getConfig()->get('offset');
		$timeZone = new \DateTimeZone($tzone);
		$date = new \DateTime(date('d-m-Y',$fecha), $timeZone);

		return $date->getTimestamp();
	}

}