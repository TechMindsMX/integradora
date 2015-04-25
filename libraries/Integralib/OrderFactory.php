<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Apr-15
 * Time: 12:42 PM
 */

namespace Integralib;

defined('_JEXEC') or die('Restricted access');

class OrderFactory {

	/**
	 * @param $order
	 * @param $orderType
	 *
	 * @return OdCompra|OdDeposito|OdRetiro|OdVenta
	 *
	 */
	public static function getOrderByIdAndType( $order, $params ) {
		switch ($params['type']) {
			case 'odv':
				$return = new OdVenta($order);
				break;
			case 'odr':
				$return = new OdRetiro($order);
				break;
			case 'odc':
				$return = new OdCompra($order);
				break;
			case 'odd':
				$return = new OdDeposito($order);
				break;
		}

		return $return;
	}
}