<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 4:26 PM
 */

namespace Integralib;

use getFromTimOne;

defined('_JEXEC') or die('Restricted access');

class OdCompra extends Order {

    public $dataBank;
    public $paymentMethod;
    protected $factura;

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
		$this->receptor = new \IntegradoSimple($order->proveedor);
	}

	private function processOrderData( $order ) {
		$this->id              = (INT)$order->id;
		$this->orderType       = 'odc';
		$this->proyecto        = (INT)$order->proyecto;
		$this->clientId        = (INT)$order->proveedor;
		$this->proveedor       = (INT)$order->proveedor;
		$this->integradoId     = (INT)$order->integradoId;
		$this->numOrden        = (INT)$order->numOrden;
		$this->paymentMethod   = (INT)$order->paymentMethod;
		$this->paymentMethod   = getFromTimOne::getPaymentMethodName($order->paymentMethod);
		$this->status          = (INT)$order->status;
		$this->totalAmount     = (FLOAT)$order->totalAmount;
		$this->createdDate     = (STRING)$order->createdDate;
		$this->paymentDate     = (STRING)$order->paymentDate;
		$this->urlXML          = (STRING)$order->urlXML;
		$this->observaciones   = (STRING)$order->observaciones;

		$this->bankId          = (INT)$order->bankId;
		$this->dataBank        = getFromTimOne::getDataBankByBankId($order->bankId);
		$this->status          = getFromTimOne::getOrderStatusName($order->status);

		$xmlFileData            = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$order->urlXML);
		$data 			        = new \xml2Array();
		$this->factura         = $data->manejaXML($xmlFileData);

		$this->subTotalAmount  = (float)$this->factura->comprobante['SUBTOTAL'];
		$this->totalAmount     = $this->factura->comprobante['TOTAL'];
		$this->iva             = $this->factura->impuestos->iva->importe;
		$this->ieps            = $this->factura->impuestos->ieps->importe;

		$o = new OrdenFn();
		$this->balance = $o->calculateBalance($this);

		$this->setEmisor($order);

		$this->setReceptor($order);

		$this->setProjectSubprojectFromOrder( $order );

		getFromTimOne::convierteFechas($this);
	}

	public function getFacturaUuid() {
		return $this->factura->complemento['children'][0]['attrs']['UUID'];
	}

}