<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelOdclist extends JModelItem {
	protected $dataModelo;
	
	public function getOrdenes($integradoId = null){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];
		$integrado 	 = new Integrado;
		$currUser	 = Jfactory::getUser();
		
		$listado = getFromTimOne::getOrdenesCompra($integradoId);
		
		return $listado;
	}
}

