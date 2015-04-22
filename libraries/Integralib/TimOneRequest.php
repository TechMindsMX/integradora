<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 05-Mar-15
 * Time: 10:40 AM
 */

namespace Integralib;

use JFactory;
use JLog;
use sendToTimOne;
use servicesRoute;

class TimOneRequest {
	public $resultado;
	protected $integradoId;
	public $objEnvio;

	function __construct() {
		$this->rutas = new servicesRoute();
	}

	/**
	 * @param $txUUID
	 *
	 * @return mixed
	 */
	public function makeRequest($datosEnvio){
		unset($this->options);

		$request = new sendToTimOne();
		$request->setServiceUrl($datosEnvio->url);
		$request->setJsonData($this->objEnvio);
		$request->setHttpType($datosEnvio->type);

		$this->resultado = $request->to_timone();

		jimport('joomla.log.log');

		JLog::addLogger(array('text_file' => date('d-m-Y').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora_txs');
		$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->objEnvio, $request) ) ) );
		JLog::add($logdata, JLog::DEBUG, 'bitacora_txs');

		return $this->resultado->code == 200;
	}

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

	public function sendCancelFactura($emisorRfc, $facturaUUID) {
		$this->objEnvio = new \stdClass();
		$this->objEnvio->uuid = $facturaUUID;
		$this->objEnvio->rfcContribuyente = $emisorRfc;
		$this->objEnvio->rfcContribuyente = 'AAD990814BP7';//		TODO: quitar mock FinkOK para producción

		return $this->makeRequest($this->rutas->getUrlService('facturacion', 'facturaCancel', 'create'));
	}

	/**
	 * @param $uuidReceptor
	 * @param $amount
	 *
	 * @return object resultado
	 */
	public function sendCashInTx($uuidReceptor, $amount) {
		$this->objEnvio = new \stdClass();
		$this->objEnvio->uuid = $uuidReceptor;
		$this->objEnvio->amount = $amount;

		$this->makeRequest($this->rutas->getUrlService('timone', 'txCashIn', 'create'));

		return $this->resultado;
	}

	public function sendValidateInvoice( $uuid ) {
		$this->objEnvio = new \stdClass();
		$this->objEnvio->xmlName = $uuid;

		$this->makeRequest($this->rutas->getUrlService('facturacion', 'facturaValidate', 'create'));

		return $this->resultado;
	}

}