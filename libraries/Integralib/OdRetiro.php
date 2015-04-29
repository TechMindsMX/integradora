<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 12:45 PM
 */

namespace Integralib;

use getFromTimOne;
use IntegradoSimple;

defined('_JEXEC') or die('Restricted access');

class OdRetiro extends Order {

	public $numOrden;
	public $paymentMethod;
	public $paymentDate;

	/**
	 * @param null $orderData
	 * @param null $orderId
	 */
	function __construct( $orderData = null, $orderId = null ) {
		if (isset($orderId)) {
			$orderData = getFromTimOne::getOrdenes(null, $orderId, 'ordenes_compra');
			$orderData = $orderData [0];
		}

		if (isset($orderData)) {
			$this->processOrderData( (object)$orderData );
		}
	}

	public function processOrderData( $order ){

		$this->id               = (INT)$order->id;
		$this->integradoId      = (INT)$order->integradoId;
		$this->setEmisor($order);
		$this->setReceptor($order);

		$this->orderType       = 'odr';
		$this->numOrden         = (INT)$order->numOrden;
		$this->paymentMethod    = getFromTimOne::getPaymentMethodName((INT)$order->paymentMethod);
		$this->status           = getFromTimOne::getOrderStatusName($order->status);
		$this->totalAmount      = (FLOAT)$order->totalAmount;
		$this->iva              = 0;
		$this->subTotalAmount  = (FLOAT)$order->totalAmount; // subtotal es el mismo que el total por no tener impuesto, el atributo es necesario para calculos
		$this->createdDate      = (STRING)$order->createdDate;
		$this->paymentDate      = (STRING)$order->paymentDate;
		$this->cuentaId         = 0;

		$this->cuenta           = $this->getReceptor()->getAccountData($this->cuentaId);

		getFromTimOne::convierteFechas($this);
		$o = new OrdenFn();
		$this->balance = $o->calculateBalance($this);

	}

	protected function setEmisor( $order ) {
		$this->emisor = new IntegradoSimple(1);
	}

	protected function setReceptor( $order ) {
		$this->receptor = new IntegradoSimple($order->integradoId);
	}


}