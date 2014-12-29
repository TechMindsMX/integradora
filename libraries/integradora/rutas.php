<?php
defined ('JPATH_PLATFORM') or die;

jimport ('joomla.factory');

/**
 * Clase rutas de servicios
 */

class timoneRoute extends servicesRoute
{

	function __construct () {
		$this->setBaseController('timone/services/integra/');
		$this->setAddress('api-stage.timone.mx/');

		parent::__construct();
	}
}

class servicesRoute
{
	private $ssl;

	protected $address = 'api.integradora.mx/';

	protected $port = '';

	private $baseController = '';

	public $baseUrl;

	public $urls;

	public function __construct () {

		$juri = JUri::getInstance ();
		$this->ssl = ($juri->getScheme() === null) ? 'http' : $juri->getScheme();

		$this->baseUrl = $this->ssl .'://'. $this->address . $this->port . $this->baseController;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl () {
		return $this->baseUrl;
	}

	public function userUrls() {
		$this->urls = new servicesUrls();

		$this->urls->setUser ();

		return $this;
	}

	public function projectUrls () {
		$this->urls = new servicesUrls();

		$this->urls->setProject ();

		return $this;
	}

	public function productUrls (){
		$this->urls = new servicesUrls();

		$this->urls->setProduct ();

		return $this;
	}

	public function comisionUrls () {
		$this->urls = new servicesUrls();

		$this->urls->setComisions ();

		return $this;
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

	public function setProject () {
		$this->list->url = 'users/{userId}/projects';
		$this->create->url = 'users/{userId}/projects';
		$this->details->url = 'users/{userId}/projects/{id}';
		$this->update->url = 'users/{userId}/projects/{id}';
		$this->disable->url = 'users/{userId}/projects/{id}';
	}

	public function setProduct () {
		$this->list->url = 'users/{userId}/products';
		$this->create->url = 'users/{userId}/products';
		$this->details->url = 'users/{userId}/products/{id}';
		$this->update->url = 'users/{userId}/products/{id}';
		$this->disable->url = 'users/{userId}/products/{id}';
	}

	public function setUser () {
		$this->list->url = 'users';
		$this->create->url = 'users';
		$this->details->url = 'users/{id}';
		$this->update->url = 'users/{id}';
		$this->disable->url = 'users/{id}';
	}

	public function setComisions () {
		$this->list->url = 'users';
		$this->create->url = 'users/create';
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
