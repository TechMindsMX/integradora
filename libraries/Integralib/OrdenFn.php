<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 6:30 PM
 */

namespace Integralib;

use getFromTimOne;
use IntegradoSimple;
use stdClass;

defined('_JEXEC') or die('Restricted access');

class OrdenFn {

	protected $minAmount;
	protected $order;

	public static function getStatusIdByName( $string ) {
		$statusCatalog = getFromTimOne::getOrderStatusCatalogByName();

		return $statusCatalog[ ucfirst(strtolower($string)) ]->id;
	}

	public static function getMinAmount() {
		return 0;
	}

	public static function getCantidadAutRequeridas(IntegradoSimple $emisor, IntegradoSimple $receptor){
		$auth = 0;
		$cant_auths = new \stdClass();

		if( $emisor->isIntegrado() ){
			$cant_auths->emisor = $auth+$emisor->getOrdersAtuhorizationParams();
		}
		if( $receptor->isIntegrado() ){
			$cant_auths->receptor = $auth+$receptor->getOrdersAtuhorizationParams();
		}

		$cant_auths->totales = array_sum((array)$cant_auths);

		return $cant_auths;
	}

	public static function getIdEmisor($order, $orderType) {
		switch ($orderType){
			case 'odv':
				$return = $order->integradoId;
				break;
			case 'odc':
				$return = $order->integradoId;
				break;
			case 'odd':
				$return = $order->integradoId;
				break;
			case 'odr':
				$return = null;
				break;
			case 'odp':
				$return = $order->integradoIdA;
				break;
			case 'mutuo':
				$return = $order->integradoIdE;
				break;
			case 'odp':
				$return = $order->acreedor;
				break;
		}

		return $return;
	}

	public static function getIdReceptor($order, $orderType) {
		switch ($orderType){
			case 'odv':
				$return = $order->clientId;
				break;
			case 'odc':
				$return = $order->proveedor;
				break;
			case 'odd':
				$return = null;
				break;
			case 'odr':
				$return = $order->integradoId;
				break;
			case 'odp':
				$return = $order->integradoIdD;
				break;
			case 'mutuo':
				$return = $order->integradoIdR;
				break;
			case 'odp':
				$return = $order->deudor;
				break;
		}

		return $return;
	}

	public static function getRelatedOdvIdFromOdcId( $id_odc ) {
		$result = getFromTimOne::selectDB('ordenes_odv_odc_relation', 'id_odc = '.(INT)$id_odc);
		return $result[0]->id_odv;
	}

	public static function sumaOrders( $orders ) {
		$neto  = 0;
		$iva   = 0;
		$total = 0;

		$obj                = new stdClass();
		$obj->pagado->total = array ();
		$obj->pagado->iva   = array ();
		$obj->pagado->neto  = array ();

		foreach ( $orders as $order ) {
			$neto  = $neto + $order->subTotalAmount;
			$iva   = $iva + $order->iva;
			$total = $total + $order->getTotalAmount();

			$montoTxs = 0;
			foreach ( $order->txs as $tx ) {
				if ( isset( $tx->detalleTx->amount ) ) {
					$montoTxs                     = $montoTxs + (FLOAT) $tx->detalleTx->amount;
					$tx->detalleTx->ivaProporcion = (FLOAT) $tx->detalleTx->amount * ( $order->iva / $order->subTotalAmount );
				}
			}
			//TODO verificar IVA de saldo
			$order->saldo->total = $order->getTotalAmount() - $montoTxs;
			$order->saldo->iva   = $montoTxs * ( $order->iva / $order->subTotalAmount );

			$obj->pagado->total[] = $montoTxs;
			$obj->pagado->iva[]   = $order->saldo->iva;
			$obj->pagado->neto[]  = $montoTxs - $order->saldo->iva;
		}

		$obj->pagado->total = array_sum( $obj->pagado->total );
		$obj->pagado->iva   = array_sum( $obj->pagado->iva );
		$obj->pagado->neto  = array_sum( $obj->pagado->neto );

		$obj->neto  = $neto;
		$obj->iva   = $iva;
		$obj->total = $total;

		return $obj;
	}

	/**
	 * @param $order
	 *
	 * @return float
	 */
	public function calculateBalance( $order ) {
		$this->order = $order;

		$this->order->sumOrderTxs = 0;
		$this->order->txs = $this->getOrderTxs();
		if ( !empty( $this->order->txs ) ) {
			foreach ( $this->order->txs as $tx ) {
				$this->order->sumOrderTxs += $tx->detalleTx->amount;
				$ivaOrderRate = ( $order->iva / $order->subTotalAmount );
				$tx->detalleTx->net = $tx->detalleTx->amount / (1+$ivaOrderRate);
				$tx->detalleTx->iva = $tx->detalleTx->net * $ivaOrderRate;
			}
		}
		if (is_a($this->order, 'stdClass')) {
			$totalAmount = $this->order->totalAmount;
		} else {
			$totalAmount = $this->order->getTotalAmount();
		}

		return $totalAmount - $this->order->sumOrderTxs;
	}

	private function getOrderTxs() {
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		if (is_a($this->order, 'stdClass')) {
			$orderType = $this->order->id;
			$statusId = $this->order->orderType;
		} else {
			$orderType = $this->order->getId();
			$statusId = $this->order->getOrderType();
		}

		$query->select('txs.id, txs.idTx, txs.idIntegrado, txs.date, txs.idComision, piv.idOrden, piv.orderType')
		      ->from($db->quoteName('#__txs_timone_mandato', 'txs') )
		      ->join('INNER', $db->quoteName('#__txs_mandatos', 'piv') . ' ON ( txs.id = piv.id )' )
		      ->where('piv.idOrden = '.$db->quote( $statusId ).' AND piv.orderType = '.$db->quote( $orderType ));
		$db->setQuery($query);
		$results = $db->loadObjectList();

		foreach ( $results as $tx ) {
			$respose = getFromTimOne::getTxDataByTxId($tx->idTx);
			$tx->detalleTx = json_decode($respose->data);;
		}

		return $results;
	}
}