<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 11-Mar-15
 * Time: 9:39 AM
 */

namespace Integralib;


class OdVenta extends Order {

	protected $totalAmount;
	protected $productos;

	/**
	 * @param $id
	 *
	 * Sets Order parameters
	 */
	public function setOrderFromId( $id ) {
		$tmp = \getFromTimOne::getOrdenesVenta(null, $id);

		foreach ( $tmp[0] as $key => $val ) {
			$this->$key = $val;
		}

		$this->emisor = new \IntegradoSimple($this->integradoId);
		$this->receptor = new \IntegradoSimple($this->clientId);

		$this->calculateTotalAmount();
	}

	/**
	 * @return object IntegradoSimple
	 */
	public function getReceptor() {
		return $this->receptor;
	}

	/**
	 * Set totalAmount in Order
	 */
	public function calculateTotalAmount() {
		$this->totalAmount = 0;

		$catalogo = new \Catalogos();
		$ivas = $catalogo->getCatalogoIVA();

		foreach ( json_decode($this->productos) as $prod ) {
			$this->totalAmount += ((float)$prod->p_unitario * (float)$prod->cantidad) * (1 + ((float)$ivas[$prod->iva]->leyenda/100));
		}
	}

	/**
	 * @return float
	 */
	public function getTotalAmount() {
		return $this->totalAmount;
	}

}