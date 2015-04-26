<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 10:38 AM
 */

namespace Integralib;

defined('_JEXEC') or die('Restricted access');

class ReportResultados extends \IntegradoOrders {

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
