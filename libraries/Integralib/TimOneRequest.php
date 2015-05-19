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

class TimOneRequest extends TimOneCurl {
	public $resultado;
	protected $objEnvio;
	protected $url;
	protected $type;

	function __construct( \urlAndType $datosEnvio, $objEnvio ) {
		$this->url = $datosEnvio->url;
		$this->type = $datosEnvio->type;

		$this->objEnvio = $objEnvio;
	}

	/**
	 * @return mixed
	 */
	public function makeRequest(){
		unset($this->options);

		$token = $this->getAccessToken();

		$this->setServiceUrl($this->url);
		$this->setJsonData(json_encode($this->objEnvio));
		$this->setHttpType($this->type);

		$this->resultado = $this->to_timone( $token );

		jimport('joomla.log.log');

		JLog::addLogger(array('text_file' => date('d-m-Y').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora_txs');
		$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->objEnvio, $this) ) ) );
		JLog::add($logdata, JLog::DEBUG, 'bitacora_txs');

		return $this->resultado;
	}

	public function getAccessToken() {
		$serviceRoute = IntFactory::getServiceRoute('timone', 'token', 'create');

		$this->setServiceUrl("http://api-qa.timone.mx/timone/oauth/token");
		$this->setJsonData('username=integradora&password=165b3c87&client_id=integra&client_secret=e6e68d8a-baf9-4880-aece-7774ffd4fb22&grant_type=password');
		$this->setHttpType('POST');

		$token = $this->to_timone();

		return json_decode($token->data);
	}

}