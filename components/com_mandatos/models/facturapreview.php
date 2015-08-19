<?php
defined('_JEXEC') or die('Restricted Access');

use Integralib\OdVenta;

class MandatosModelFacturapreview extends JModelItem {

	public $factura;
	protected $integradoId;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray( array('facturanum' => 'INT') );

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		parent::__construct();
	}

	public function getFacturas(){
		$xml = new xml2Array();
		$cats = $this->getCatalogos();

		if (!isset($facturas)) {
			$this->factura = new OdVenta();
			$this->factura->setOrderFromId($this->inputVars['facturanum']);

			$this->factura->currency 	= $cats[0]->code;
			$this->factura->project 	= isset( $this->factura->projectId ) ? new \Integralib\Project($this->factura->projectId) : null;
			$this->factura->subProject 	= isset( $this->factura->projectId2 ) ? new \Integralib\Project($this->factura->projectId2) : null;
			$odvXML 					= file_get_contents($this->factura->urlXML);
			$this->factura->datosXML 	= $xml->manejaXML($odvXML);
		}

		// Verifica si la FACTURA exite para el integrado o redirecciona
		if (is_null($this->factura)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('FACTURA_INVALID'), 'error');
		}

		return $this->factura;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->integradoId);
	}

	private function getCatalogos() {
		$cat = new Catalogos();

		return $cat->getCurrencies();
	}

}