<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 25-Mar-15
 * Time: 12:50 PM
 */
namespace Integralib;

interface TimOneRequestInterface {
	/**
	 * @param \urlAndType $datosEnvio
	 * @param $objEnvio
	 *
	 * @return mixed
	 * @internal param $txUUID
	 *
	 */
	public function makeRequest( \urlAndType $datosEnvio, $objEnvio );

}