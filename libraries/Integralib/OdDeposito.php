<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 12:48 PM
 */

namespace Integralib;

use getFromTimOne;
use IntegradoSimple;

defined('_JEXEC') or die('Restricted access');

class OdDeposito extends Order {

	function __construct($order = null) {
		if (isset($order)) {
			$this->id = $order['id'];
			$this->setOrderFromId();
		}
	}

	function getAll( $integradoId, $idOrden ) {
		return getFromTimOne::getOrdenes($integradoId, $idOrden, 'ordenes_deposito');
	}

	/**
	 * @param $id
	 *
	 * Sets Order parameters
	 */
	public function setOrderFromId() {
		$order = getFromTimOne::getOrdenes(null, $this->id, 'ordenes_deposito');
		$order = $order[0];

		$this->id              = (INT)$order->id;
		$this->integradoId     = (INT)$order->integradoId;
		$this->orderType       = 'odd';
		$this->numOrden        = (INT)$order->numOrden;
		$this->status          = (INT)$order->status;
		$this->paymentMethod   = (INT)$order->paymentMethod;
		$this->totalAmount     = (FLOAT)$order->totalAmount;
		$this->iva              = 0;
		$this->subTotalAmount  = (FLOAT)$order->totalAmount; // subtotal es el mismo que el total por no tener impuesto, el atributo es necesario para calculos
		$this->attachment      = (STRING)$order->attachment;
		$this->createdDate     = (STRING)$order->createdDate;
		$this->paymentDate     = (STRING)$order->paymentDate;

		$this->status = getFromTimOne::getOrderStatusName($order->status);
		$this->paymentMethod   = getFromTimOne::getPaymentMethodName($order->paymentMethod);

		$this->setEmisor($this);
		$this->setReceptor($this);

		$fn = new OrdenFn();
		$this->totalCalculated = $fn->calculateBalance($this);

		getFromTimOne::convierteFechas($this);
	}

	/**
	 * @param $order
	 *
	 * @return void
	 */
	protected function setEmisor( $order ) {
		$this->emisor =  new \IntegradoSimple( $order->integradoId );
	}

	/**
	 * @param $order
	 *
	 * @return void
	 */
	protected function setReceptor( $order ) {
		$this->receptor =  new \IntegradoSimple( 93 );
	}
}