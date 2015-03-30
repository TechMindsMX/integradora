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

		$this->setServiceUrl($this->url);
		$this->setJsonData(json_encode($this->objEnvio));
		$this->setHttpType($this->type);

		$this->resultado = $this->to_timone();

		jimport('joomla.log.log');

		JLog::addLogger(array('text_file' => date('d-m-Y').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora_txs');
		$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->objEnvio, $this) ) ) );
		JLog::add($logdata, JLog::DEBUG, 'bitacora_txs');

		return $this->resultado;
	}

}