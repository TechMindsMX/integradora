<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 26-Feb-15
 * Time: 4:46 PM
 */

namespace Integralib;

class Cliente {

	public function getAllActive($integradoId) {
		$clientes = \getFromTimOne::getClientes($integradoId);

		$response = array_filter($clientes, function($item){
			return ($item->status == 1 && in_array($item->type, array(0,2)) );
		});

		return $response;
	}
}