<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 05-Mar-15
 * Time: 10:40 AM
 */

namespace Integralib;

use sendToTimOne;
use servicesRoute;

class TimOneRequest {
	protected $integradoId;

	/**
	 * @param $txUUID
	 *
	 * @return mixed
	 */
	public function getTxDetails($txUUID) {
		$rutas = new servicesRoute();

		$params = $rutas->getUrlService('timone','txDetails','details');

		$serviceUrl = str_replace('{uuid}', $txUUID, $params->url);
		$jsonData = '';
		$httpType = $params->type;

		$request = new sendToTimOne();

		$request->setServiceUrl($serviceUrl);
		$request->setJsonData($jsonData);
		$request->setHttpType($httpType);

		$result = $request->to_timone(); // realiza el envio

		return $result;
	}

}