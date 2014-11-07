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
        $post       = array('integradoId' => 'INT');
		$data		= JFactory::getApplication()->input->getArray($post);
		$productos	= getFromTimOne::getProducts($data['integradoId']);
		
		return $productos;
	}
	
}

