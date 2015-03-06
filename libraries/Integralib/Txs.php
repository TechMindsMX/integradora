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

		return $this->tx->amount - $this->tx->sumOrderTxs;
	}

	private function getTxOrders() {
		$db    = \JFactory::getDbo();
		$query = $db->getQuery( true );

		$query->select( '*' )
		      ->from( '#__txs_mandatos' )
		      ->where( 'idTx = ' . $db->quote( $this->tx->id ) );
		$db->setQuery( $query );
		$resutls = $db->loadObjectList();

		return $resutls;
	}


}