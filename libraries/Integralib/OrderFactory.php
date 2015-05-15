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
	 * @param $orderId
	 * @param $orderType
	 *
	 * @param $orderData
	 *
	 * @return OdCompra|OdDeposito|OdRetiro|OdVenta
	 */
	public static function getOrder( $orderId = null, $orderType, $orderData = null ) {
		switch ($orderType) {
			case 'odv':
				$return = new OdVenta($orderData, $orderId);
				break;
			case 'odr':
				$return = new OdRetiro($orderData, $orderId);
				break;
			case 'odc':
				$return = new OdCompra($orderData, $orderId);
				break;
			case 'odd':
				$return = new OdDeposito($orderData, $orderId);
				break;
			default:
				$return = null;
		}

		return $return;
	}
}