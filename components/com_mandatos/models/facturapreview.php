<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelFacturapreview extends JModelItem {

	public $factura;
	protected $integradoId;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray(array('facturanum', null, 'INT'));

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		parent::__construct();
	}

	public function getFacturas(){

		if (!isset($facturas)) {
			$facturas = getFromTimOne::getFacturasVenta($this->integradoId);
		}

		foreach ($facturas as $key => $value) {
			if ($value->id == $this->inputVars['facturanum'] ) {
				$this->factura = $value;
			}
		}

		// Verifica si la FACTURA exite para el integrado o redirecciona
		if (is_null($this->factura)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->integradoId), JText::_('FACTURA_INVALID'), 'error');
		}

		return $this->factura;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->integradoId);
	}

}