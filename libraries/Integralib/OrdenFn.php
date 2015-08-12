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
		$cant_auths = new \stdClass();

		if( $emisor->isIntegrado() ){
			$cant_auths->emisor = $emisor->getOrdersAtuhorizationParams();
		}
		if( $receptor->isIntegrado() ){
			$cant_auths->receptor = $receptor->getOrdersAtuhorizationParams();
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

    public static function getRelatedOdcIdFromOdvId( $id_odv ) {
        $result = getFromTimOne::selectDB('ordenes_odv_odc_relation', 'id_odv = '.(INT)$id_odv);
        return $result[0]->id_odc;
    }

	public static function sumaOrders( $orders ) {
		$obj                = new stdClass();

		$obj->nominal->neto     = array ();
		$obj->nominal->iva      = array ();
		$obj->nominal->total    = array ();
		$obj->pagado->total     = array ();
		$obj->pagado->iva       = array ();
		$obj->pagado->neto      = array ();

		if ( ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				$obj->nominal->neto[]   = $order->subTotalAmount;
				$obj->nominal->iva[]    = $order->iva;
				$obj->nominal->total[]   = $order->getTotalAmount();

				$montoTxs = 0;
				foreach ( $order->txs as $tx ) {
					if ( isset( $tx->detalleTx->amount ) ) {
						$montoTxs                     = $montoTxs + (FLOAT) $tx->detalleTx->amount;
						$tx->detalleTx->ivaProporcion = (FLOAT) $tx->detalleTx->amount * ( $order->iva / $order->subTotalAmount );
					}
				}
				//TODO verificar IVA de saldo
				$factor = $montoTxs / $order->getTotalAmount();
				$order->saldo->total = $order->getTotalAmount() - $montoTxs;
				$order->saldo->iva   = $order->iva - ( $factor * $order->iva);
				$order->saldo->net   = $order->subTotalAmount - ($factor * $order->subTotalAmount);

				$obj->pagado->total[] = $montoTxs;
				$obj->pagado->iva[]   = $order->iva - $order->saldo->iva;
				$obj->pagado->neto[]  = $montoTxs - ($order->iva - $order->saldo->iva);
			}
		}

		$obj->pagado->total = array_sum( $obj->pagado->total );
		$obj->pagado->iva   = array_sum( $obj->pagado->iva );
		$obj->pagado->neto  = array_sum( $obj->pagado->neto );

		$obj->nominal->neto  = array_sum($obj->nominal->neto );
		$obj->nominal->iva   = array_sum($obj->nominal->iva  );
		$obj->nominal->total = array_sum($obj->nominal->total);

		$obj->saldo->neto  = $obj->nominal->neto  - $obj->pagado->neto ;
		$obj->saldo->iva   = $obj->nominal->iva   - $obj->pagado->iva  ;
		$obj->saldo->total = $obj->nominal->total - $obj->pagado->total;

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
				$this->order->sumOrderTxs += $tx->assignedAmount;
				if ( isset( $order->subTotalAmount ) ) {
					$ivaOrderRate = ( $order->iva / $order->subTotalAmount );
				} else {
					$ivaOrderRate = 0;
				}
				$tx->detalleTx->net = $tx->assignedAmount / (1+$ivaOrderRate);
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
			$statusId = $this->order->id;
			$orderType= $this->order->orderType;
		} else {
			$statusId = $this->order->getId();
			$orderType = $this->order->getOrderType();
		}

		if ($orderType == 'odv') {

			if (is_a($this->order, 'stdClass')) {
				$odv = OrderFactory::getOrder($this->order->id, 'odv');
				$orderId = $odv->getRelatedOdcId();
			} else {
				$orderId = $this->order->getRelatedOdcId();
			}
			$query->select( $db->quoteName( array (
				                                'txs.id',
				                                'txs.idTx',
				                                'txs.integradoId',
				                                'txs.date',
				                                'txs.idComision',
				                                'piv.idOrden',
				                                'piv.orderType',
				                                'piv.amount'
			                                ), array (
				                                'id',
				                                'idTx',
				                                'idIntegrado',
				                                'date',
				                                'idComision',
				                                'idOrden',
				                                'orderType',
				                                'assignedAmount'
			                                ) ) )
			      ->from( $db->quoteName( '#__txs_timone_mandato', 'txs' ) )
			      ->join( 'INNER', $db->quoteName( '#__txs_mandatos', 'piv' ) . ' ON ( txs.id = piv.id )' )
			      ->where( 'piv.idOrden = ' . $db->quote( $orderId ) . ' AND piv.orderType = ' . $db->quote( 'odc' ) );
			$db->setQuery( $query );
			$results = $db->loadObjectList( 'id' );

		} else {
			$query->select( $db->quoteName( array (
				                                'txs.id',
				                                'txs.idTx',
				                                'txs.integradoId',
				                                'txs.date',
				                                'txs.idComision',
				                                'piv.idOrden',
				                                'piv.orderType',
				                                'piv.amount'
			                                ), array (
				                                'id',
				                                'idTx',
				                                'idIntegrado',
				                                'date',
				                                'idComision',
				                                'idOrden',
				                                'orderType',
				                                'assignedAmount'
			                                ) ) )
			      ->from( $db->quoteName( '#__txs_timone_mandato', 'txs' ) )
			      ->join( 'INNER', $db->quoteName( '#__txs_mandatos', 'piv' ) . ' ON ( txs.id = piv.id )' )
			      ->where( 'piv.idOrden = ' . $db->quote( $statusId ) . ' AND piv.orderType = ' . $db->quote( $orderType ) );
			$db->setQuery( $query );
			$results = $db->loadObjectList( 'id' );
		}

        $timoneRequest = new TimOneRequest();

		foreach ( $results as $tx ) {
			$respose = $timoneRequest->getTxDetails($tx->idTx);
			$tx->detalleTx = json_decode($respose->data);;
		}

		return $results;
	}
}