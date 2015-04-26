<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 26-Apr-15
 * Time: 12:07 PM
 */

namespace Integralib;

class Tx {

	public $amount;
	public $id;
	public $order;

	public function getTotalAmount() {
		return (FLOAT)$this->amount;
	}

	public function getTxData() {
		return $this->order->txs[$this->id]->detalleTx;
	}
}