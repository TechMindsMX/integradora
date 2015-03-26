<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 25-Mar-15
 * Time: 12:50 PM
 */
namespace Integralib;

class IntFactory {

	public static function getTimoneRequest(\urlAndType $datosEnvio, $objEnvio) {
		return new TimOneRequest($datosEnvio, $objEnvio);
	}

	/**
	 * @param $controller
	 * @param $service
	 * @param $action
	 *
	 * @return \urlAndType
	 */
	public static function getServiceRoute($controller, $service, $action) {
		$r = new \servicesRoute();
		return $r->getUrlService($controller, $service, $action);
	}

}