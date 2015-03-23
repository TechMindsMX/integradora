<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Mar-15
 * Time: 1:57 PM
 */

namespace Integralib;

defined('_JEXEC') or die('Restricted access');

class TxLiquidacion {

	protected $amount;
	protected $integradoId;

	/**
	 * @return mixed
	 */
	public function getAmount() {
		return (float)$this->amount;
	}

	/**
	 * @return mixed
	 */
	public function getIntegradoId() {
		return $this->integradoId;
	}

	/**
	 * @return mixed
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param $monto
	 * @param $integradoId
	 */
	public function saveNewTx( $monto, $integradoId ) {
		$obj = new \stdClass();
		$obj->amount = $monto;
		$obj->integradoId = $integradoId;

		$db = \JFactory::getDbo();

		$db->insertObject('#__txs_liquidacion_saldo', $obj);
	}

}