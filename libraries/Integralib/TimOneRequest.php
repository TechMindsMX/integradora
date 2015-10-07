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

class TimOneRequest extends TimOneCurl {
    protected   $url;
    protected   $type;
    public      $resultado;
    protected   $integradoId;
    public      $objEnvio;

    function __construct() {
    }

    /**
     * @return mixed
     */
    public function makeRequest($datosEnvio){
        unset($this->options);

        @$this->objEnvio = !isset($this->objEnvio)  ? $datosEnvio->objEnvio : $this->objEnvio;

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

    public function getUserTxs($userUuis) {
        $rutas = new servicesRoute();

        $params = $rutas->getUrlService('timone','userTxs','list');

        $serviceUrl = str_replace('{uuid}', $userUuis, $params->url);
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
        $rutas          = new servicesRoute();
        $this->objEnvio = new \stdClass();

        $this->objEnvio->uuid             = $facturaUUID;
        $this->objEnvio->rfcContribuyente = $emisorRfc;
        $this->objEnvio->rfcContribuyente = 'AAD990814BP7';//		TODO: quitar mock FinkOK para producción

        return $this->makeRequest($rutas->getUrlService('facturacion', 'facturaCancel', 'create'));
    }

    /**
     * @param $uuidReceptor
     * @param $amount
     *
     * @return object resultado
     */
    public function sendCashInTx($uuidEmisor, $amount) {
        $servicesRoute = new servicesRoute();
        $url = $servicesRoute->getUrlService('timone', 'txCashIn', 'create');

        $url->objEnvio->uuid = $uuidEmisor;
        $url->objEnvio->amount = $amount;


        $this->makeRequest($url);

        return $this->resultado;
    }

    public function sendInvoiceToValidation( $uuid ) {
        $this->objEnvio = new \stdClass();
        $this->objEnvio->xmlName = $uuid;

        $this->makeRequest($this->rutas->getUrlService('facturacion', 'facturaValidate', 'create'));

        return $this->resultado;
    }

    public function getAccessToken() {
//		$serviceRoute = IntFactory::getServiceRoute('timone', 'token', 'create');

        $this->setServiceUrl(TOKEN_ROUTE."token");
        $this->setJsonData('username=' . OAUTH_USERNAME . '&password=' . OAUTH_PASSWORD . '&client_id=' . OAUTH_CLIENT_ID . '&client_secret=' . OAUTH_CLIENT_SECRET . '&grant_type=' . OAUTH_GRANT_TYPE );
        $this->setHttpType('POST');

        $token = $this->to_timone();

        return json_decode($token->data);
    }

    public function getFacturacionAccessToken() {
        $this->setServiceUrl(TOKEN_FACT_ROUTE."token");
        $this->setJsonData('username=' . OAUTH_USERNAME . '&password=' . OAUTH_PASSWORD . '&client_id=' . OAUTH_CLIENT_ID . '&client_secret=' . OAUTH_CLIENT_SECRET . '&grant_type=' . OAUTH_GRANT_TYPE );
        $this->setHttpType('POST');

        $token = $this->to_timone();

        return json_decode($token->data);
    }

	public function getListBankCodes( ){
		$serviceUrl = MIDDLE . TIMONE . 'stp/listBankCodes';
		$jsonData = '';
		$httpType = 'GET';

		$request = new sendToTimOne();

		$request->setServiceUrl($serviceUrl);
		$request->setJsonData($jsonData);
		$request->setHttpType($httpType);

		$result = $request->to_timone(); // realiza el envio

		return json_decode(@$result->data);
	}

}