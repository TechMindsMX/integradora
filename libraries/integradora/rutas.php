<?php
defined ('JPATH_PLATFORM') or die;

jimport ('joomla.factory');

/**
 * Clase rutas de servicios
 */

class servicesRoute extends servicesUrls
{
	private $ssl;

	protected $timone = 'api-stage.timone.mx/services/integra/';

    protected $facturacion = 'api.timone-factura.mx/factura/';

	protected $port = '';

	public $baseUrl;

	public $urls;

	public function __construct () {

		$juri = JUri::getInstance ();
		$this->ssl = ($juri->getScheme() === null) ? 'http' : $juri->getScheme();

		$this->baseUrl = $this->ssl .'://'. $this->facturacion . $this->port;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl () {
		return $this->baseUrl;
	}



	/**
	 * @param string $baseController
	 */
	public function setBaseController ($baseController) {
		$this->baseController = $baseController;
	}

	/**
	 * @param string $address
	 */
	public function setAddress ($address) {
		$this->address = $address;
	}


}

class servicesUrls
{

	public $list;
	public $create;
	public $update;
	public $disable;
	public $details;

	public function __construct () {
		$this->list = new urlAndType();
		$this->create = new urlAndType();
		$this->details = new urlAndType();
		$this->update = new urlAndType();
		$this->disable = new urlAndType();

		$this->list->type = "GET";
		$this->create->type = 'POST';
		$this->details->type = 'GET';
		$this->update->type = 'PUT';
		$this->disable->type = 'DELETE';
	}

	public function setUser () {
		$this->list->url = 'users';
		$this->create->url = 'users';
		$this->details->url = 'users/{id}';
		$this->update->url = 'users/{id}';
		$this->disable->url = 'users/{id}';
	}


}

class urlAndType
{
	public $url;
	public $type;
}
