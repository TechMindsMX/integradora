<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.factory');


/**
 * Clase rutas de servicios
 */
class IntRoute {

	protected $ssl;

	protected $schema = MIDDLE;

	protected $ip = TIMONE;
	
	protected $port = PUERTO;
	
	protected $urls;

	private function getUrl() {
		
		$juri = JUri::getInstance();
		
		$this->ssl = $juri->getScheme();

		$url = $this->ssl.$this->ip.$this->port.$this->urls;

		return $url;
	}

	public function saveComisionServiceUrl () {
		$this->urls = 'comisions/save';

		return $this->getUrl();
	}

}
