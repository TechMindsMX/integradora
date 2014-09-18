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
class MandatosModelProductoslist extends JModelItem {
	
	protected $dataModelo;
	
	public function getProductos($integradoId = null){
		$data			= JFactory::getApplication()->input->getArray();
		$joomlaId		= JFactory::getUser()->id;
		$integradoId	= $data['integradoId'];	
		$productos		= getFromTimOne::getProducts($integradoId);
		
		$this->dataModelo = $productos;
		
		return $this->dataModelo;
	}
	
}

