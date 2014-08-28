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
class MandatosModelProductos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProductos($integradoId = null){
		$joomlaId		= JFactory::getUser()->id;
		$integradoId	= getFromTimOne::getIntegradoId($joomlaId);	
		$productos		= getFromTimOne::getProducts($integradoId['integrado_id']);
		
		$this->dataModelo = $productos;
		
		return $this->dataModelo;
	}
	
}

