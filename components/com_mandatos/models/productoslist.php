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
	public function getProductos(){
		$session            = JFactory::getSession();
		$integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$productos	= getFromTimOne::getProducts($integradoId);
		
		return $productos;
	}

	public function getCatalogoIva(){
		$catalogos = new Catalogos();

		return $catalogos->getCatalogoIVA();
	}
	
}

