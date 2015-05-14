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

}