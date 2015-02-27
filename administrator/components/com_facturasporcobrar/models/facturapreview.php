<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');


class FacturasporcobrarModelFacturapreview extends JModelItem {

	public $factura;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();
        $this->integradoId       = $this->inputVars['integradoId'];

		parent::__construct();
	}

	public function getFacturas(){
		if (!isset($facturas)) {
			$facturas =  getFromTimOne::getOrdenesVenta($this->integradoId);
		}

		foreach ($facturas as $key => $value) {
			if ($value->id == $this->inputVars['facturanum'] ) {
				$this->factura = $value;
			}
		}

		return $this->factura;
	}

	public function getIntegrado()	{
        $integrados = getFromTimOne::getintegrados();
		return $integrados;
	}

}