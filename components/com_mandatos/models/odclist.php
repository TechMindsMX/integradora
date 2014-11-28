<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de lordenes de compra de un integrado, asi como la manipulacion de los datos para sacar productos y montos de la factura guardada
 */
class MandatosModelOdclist extends JModelItem {
	protected $dataModelo;
	
	public function getOrdenes(){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];

		$listado = getFromTimOne::getOrdenesCompra($integradoId);

        return $listado;
	}
}

