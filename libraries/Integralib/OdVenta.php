<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 11-Mar-15
 * Time: 9:39 AM
 */

namespace Integralib;

use getFromTimOne;

class OdVenta extends Order {

	protected $totalAmount;
	protected $productos;

	/**
	 * @param null $orderId
	 * @param null $orderData
	 */
	function __construct( $orderData = null, $orderId = null ) {
		if (isset($orderId)) {
			$orderData = getFromTimOne::getOrdenes(null, $orderId, 'ordenes_venta');
			$orderData = $orderData [0];
		}

		if (isset($orderData)) {
			$this->processOrderData( (object)$orderData );
		}
	}

	/**
	 * @param $id
	 *
	 * Sets Order parameters
	 */
	public function setOrderFromId( $id ) {
		$tmp = getFromTimOne::getOrdenesVenta(null, $id);

		foreach ( $tmp[0] as $key => $val ) {
			$this->$key = $val;
		}

		$this->calculateTotalAmount();
	}

	/**
	 * Set totalAmount in Order
	 */
	public function calculateTotalAmount() {
		$this->totalAmount = 0;
		$tmpIva = 0;
		$tmpIeps = 0;

		$catalogo = new \Catalogos();
		$ivas = $catalogo->getCatalogoIVA();

		foreach ( json_decode($this->productos) as $prod ) {
			$subtotalNetProd = ((float)$prod->p_unitario * (float)$prod->cantidad);
			$tmpIva = $subtotalNetProd * ((float)$ivas[$prod->iva]->leyenda/100);
			$tmpIeps = $subtotalNetProd * (float)$prod->ieps/100;
			$this->totalAmount +=  $subtotalNetProd + $tmpIva + $tmpIeps;
		}
	}

	/**
	 * @return float
	 */
	public function getTotalAmount() {
		return $this->totalAmount;
	}

	/**
	 * @return UUID factura from xmlfile
	 */
	public function getfacturaUUID() {
		$xml = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->urlXML);

		return \Factura::getXmlUUID($xml);
	}

	public function getMontoTotalIVA() {
		return $this->iva;
	}

	public function getMontoTotalIEPS() {
		return $this->ieps;
	}

	/**
	 * @param $order
	 *
	 * @return void
	 */
	protected function setEmisor( $order ) {
		$this->emisor = new \IntegradoSimple($this->integradoId);
	}

	/**
	 * @param $order
	 *
	 * @return void
	 */
	protected function setReceptor( $order ) {
		$this->receptor = new \IntegradoSimple($this->clientId);
	}

	private function processOrderData( $order ) {
		$catalogo = new \Catalogos();
		$catalogoIva = $catalogo->getCatalogoIVA();

		$this->id             = (INT)$order->id;
		$this->integradoId    = (INT)$order->integradoId;
		$this->orderType      = 'odv';
		$this->numOrden       = (INT)$order->numOrden;
		$this->proyecto       = (INT)$order->projectId2==0?$order->projectId:$order->projectId2;
		$this->clientId       = (INT)$order->clientId;
		$this->account        = (INT)$order->account;
		$this->paymentMethod   = getFromTimOne::getPaymentMethodName($order->paymentMethod);
		$this->conditions     = (INT)$order->conditions;
		$this->placeIssue     = getFromTimOne::getNombreEstado($order->placeIssue);
		$this->status         = (INT)$order->status;
		$this->productos      = (STRING)$order->productos;
		$this->createdDate    = (STRING)$order->createdDate;
		$this->paymentDate    = (STRING)$order->paymentDate;

		$subTotalOrden        = 0;
		$subTotalIva          = 0;
		$subTotalIeps         = 0;

		$this->productosData = json_decode($order->productos);

		foreach ($this->productosData  as $producto ) {
			$producto->iva = $catalogoIva[$producto->iva]->leyenda;

			$subTotalOrden  = $subTotalOrden + $producto->cantidad * $producto->p_unitario;
			$subTotalIva    = $subTotalIva + ($producto->cantidad * $producto->p_unitario) * ($producto->iva/100);
			$subTotalIeps   = $subTotalIeps + ($producto->cantidad * $producto->p_unitario) * ($producto->ieps/100);
		}

		$this->subTotalAmount = (float)$subTotalOrden;
		$this->totalAmount    = $subTotalOrden + $subTotalIva + $subTotalIeps;
		$this->iva      = $subTotalIva;
		$this->ieps     = $subTotalIeps;

		$o = new OrdenFn();
		$order->balance = $o->calculateBalance($this);

		$this->setEmisor($order);
		$this->setReceptor($order);

		$this->setProjectSubprojectFromOrder($order);
		$this->status = getFromTimOne::getOrderStatusName($order->status);

		getFromTimOne::convierteFechas($this);
	}

    public function getStatus(){
        return $this->status;
    }

    /**
     * @param string $productos
     */
    public function setProductos($productos){
        $this->productos = $productos;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }
}