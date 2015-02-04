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

	public function __construct () {

		$juri = JUri::getInstance ();
		$this->ssl = ($juri->getScheme() === null) ? 'http' : $juri->getScheme();

        parent::__construct();
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
	    $this->controllers = new stdClass();
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
		$this->list->url    = 'users';
		$this->create->url  = 'users';
		$this->details->url = 'users/{uuid}';
		$this->update->url  = 'users/{uuid}';
		$this->disable->url = 'users/{uuid}';
	}

	public function cashOut () {
		$this->list->url    = 'stp/cashout';
		$this->create->url  = 'stp/cashout';
		$this->details->url = 'stp/cashout/{uuid}';
		$this->update->url  = 'stp/cashout/{uuid}';
		$this->disable->url = 'stp/cashout/{uuid}';

	}

    public function transferFunds(){
        $this->list->url    = 'tx/transferFunds';
        $this->create->url  = 'tx/transferFunds';
        $this->details->url = 'tx/transferFunds/{uuid}';
        $this->update->url  = 'tx/transferFunds/{uuid}';
        $this->disable->url = 'tx/transferFunds/{uuid}';
    }

	public function userTxs( ){
		$this->list->url = 'tx/{uuid}';
	}

	public function txDetails() {
		$this->details->url = 'tx/getTransaction/{uuid}';
	}

	public function txByDateRange() {
		$this->list->url = 'tx/getTransactions/{uuid}/{startDate}/{endDate}';
	}

	public function factura() {
		$this->create->url = 'create';
	}

	public function facturaCancel() {
		$this->create->url = 'cancel';
	}

	public function facturaValidate() {
		$this->create->url = 'validate';
	}

	public function validateXml() {
		$this->create->url = 'validate';
	}
}

class urlAndType
{
	public $url;
	public $type;
}
