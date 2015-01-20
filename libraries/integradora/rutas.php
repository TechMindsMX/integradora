<?php
defined ('JPATH_PLATFORM') or die;

jimport ('joomla.factory');

/**
 * Clase rutas de servicios
 */

class servicesRoute extends servicesUrls
{
	private $ssl;

	protected $port = '';

	public $baseUrl;


	public function __construct () {

		$juri = JUri::getInstance ();
		$this->ssl = ($juri->getScheme() === null) ? 'http' : $juri->getScheme();

        parent::__construct();
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


    public function getUrlService($controller, $service, $action){
        call_user_func (array ('servicesRoute', $service));
        $respuesta = new urlAndType();

        $respuesta->url = $this->ssl.'://'.$this->controllers->$controller.$this->$action->url;
        $respuesta->type = $this->$action->type;

        return $respuesta;
    }

}

class timOneControllers{
    public $controllers;

    public function __construct(){
        $this->controllers->timone       = 'api-stage.timone.mx/timone/services/integra/';
        $this->controllers->facturacion  = 'api.timone-factura.mx/factura/';
    }
}

class servicesUrls extends timOneControllers
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

        parent::__construct();
	}

	public function user () {
		$this->list->url = 'users';
		$this->create->url = 'users';
		$this->details->url = 'users/{id}';
		$this->update->url = 'users/{id}';
		$this->disable->url = 'users/{id}';
	}

	public function cashOut () {
		$this->list->url    = 'stp/cashout';
		$this->create->url  = 'stp/cashout';
		$this->details->url = 'stp/cashout/{id}';
		$this->update->url  = 'stp/cashout/{id}';
		$this->disable->url = 'stp/cashout/{id}';

	}

    public function transferFunds(){
        $this->list->url    = 'tx/transferFunds';
        $this->create->url  = 'tx/transferFunds';
        $this->details->url = 'tx/transferFunds/{id}';
        $this->update->url  = 'tx/transferFunds/{id}';
        $this->disable->url = 'tx/transferFunds/{id}';
    }

}

class urlAndType
{
	public $url;
	public $type;
}
