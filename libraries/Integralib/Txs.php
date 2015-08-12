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
	protected $rutas;

	/**
	 * @param $txUUID
	 *
	 * @return mixed
	 */
	public function getTxDetails($txUUID) {

		$params = IntFactory::getServiceRoute('timone','txDetails','details');

        $params->url = str_replace('{uuid}', $txUUID, $params->url);
		$jsonData = '';

		$request = IntFactory::getTimoneRequest($params, $jsonData);

		return $request->makeRequest($params);
	}

	public function sendCashInTx($uuidReceptor, $amount) {
		$objEnvio = new \stdClass();
		$objEnvio->uuid = $uuidReceptor;
		$objEnvio->amount = $amount;

		$urlAndType = IntFactory::getServiceRoute('timone', 'txCashIn', 'create');

		$request = IntFactory::getTimoneRequest($urlAndType, $objEnvio);

		return $request->makeRequest();
	}

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
		      ->where( 'id = ' . $db->quote( $this->tx->id ).' AND IdOrden IS NOT NULL' );
		$db->setQuery( $query );
		$resutls = $db->loadObjectList();

		return $resutls;
	}

	public function getAmountFromDetails() {
		$amount = isset($this->tx->details->amount) ? (FLOAT)$this->tx->details->amount : 0;

		return $amount;
	}

}