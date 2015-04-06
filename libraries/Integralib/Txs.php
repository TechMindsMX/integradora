<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 06-Mar-15
 * Time: 3:22 PM
 */

namespace Integralib;

jimport('integradora.gettimone');

class Txs {
	public $tx;

	public function calculateBalance( $tx ) {
		$this->tx = $tx;

		$this->tx->sumOrderTxs = 0;
		$this->tx->txs = $this->getTxOrders();
		foreach ( $this->tx->txs as $tx ) {
			$this->tx->sumOrderTxs = $this->tx->sumOrderTxs + $tx->amount;
		}

		$txDetails = \getFromTimOne::getTxDataByTxId($this->tx->idTx);

		$this->tx->details = json_decode( $txDetails->data );

		return $this->getAmountFromDetails() - $this->tx->sumOrderTxs;
	}

	private function getTxOrders() {
		$db    = \JFactory::getDbo();
		$query = $db->getQuery( true );

		$query->select( '*' )
		      ->from( '#__txs_mandatos' )
		      ->where( 'id = ' . $db->quote( $this->tx->id ) );
		$db->setQuery( $query );
		$resutls = $db->loadObjectList();

		return $resutls;
	}

	public function getAmountFromDetails() {
		$amount = isset($this->tx->details->amount) ? (FLOAT)$this->tx->details->amount : 0;

		return $amount;
	}

}