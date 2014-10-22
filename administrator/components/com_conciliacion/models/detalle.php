<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 22-Oct-14
 * Time: 3:57 PM
 */

jimport('integradora.gettimone');

class ConciliacionModelDetalle extends JModelLegacy {

	public function getTxSTPById() {

		$ref = JFactory::getApplication()->input->get('refnum', null, 'string');

		$tx = getFromTimOne::getTxSTPbyRef($ref);

		return $tx;
	}

	public function getODVs() {
		return getFromTimOne::getOrdenesVenta(null);
	}
	public function getODCs() {
		return getFromTimOne::getOrdenesCompra(null);
	}
	public function getODDs() {
		return getFromTimOne::getOrdenesDeposito(null);
	}
	public function getODRs() {
		return getFromTimOne::getOrdenesRetiro(null);
	}
}