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
use servicesRoute;

class TimOneRequest implements TimOneRequestInterface {
	public $resultado;
	protected $integradoId;
	protected $objEnvio;

	function __construct() {
		$this->rutas = new servicesRoute();
	}

	/**
	 * @param \urlAndType $datosEnvio
	 * @param $objEnvio
	 *
	 * @return mixed
	 * @internal param $txUUID
	 *
	 */
	public function makeRequest(\urlAndType $datosEnvio, $objEnvio){
		unset($this->options);

		$request = new TimOneCurl();
		$request->setServiceUrl($datosEnvio->url);
		$request->setJsonData(json_encode($this->objEnvio));
		$request->setHttpType($datosEnvio->type);

		$this->resultado = $request->to_timone();

		jimport('joomla.log.log');

		JLog::addLogger(array('text_file' => date('d-m-Y').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora_txs');
		$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->objEnvio, $request) ) ) );
		JLog::add($logdata, JLog::DEBUG, 'bitacora_txs');

		return $this->resultado;
	}

	public function getTxDetails($txUUID) {
		$rutas = new servicesRoute();

		$params = $rutas->getUrlService('timone','txDetails','details');

		$serviceUrl = str_replace('{uuid}', $txUUID, $params->url);
		$jsonData = '';
		$httpType = $params->type;

		$request = new TimOneCurl();

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

		return $this->makeRequest($this->rutas->getUrlService('facturacion', 'facturaCancel', 'create'), $this->objEnvio);
	}

	public function sendCashInTx($uuidReceptor, $amount) {
		$this->objEnvio = new \stdClass();
		$this->objEnvio->uuid = $uuidReceptor;
		$this->objEnvio->amount = $amount;

		return $this->makeRequest($this->rutas->getUrlService('timone', 'txCashIn', 'create'));
	}



}