<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class ProductosModelProductos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProductos($integradoId = null){
		$productos = getFromTimOne::getProducts(JFactory::getUser()->id);
		
		$this->dataModelo = $productos;
		
		return $this->dataModelo;
	}
	
}

