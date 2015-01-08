<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de ordenes de prestamo de un integrado, dado un id de mutuo
 */
class MandatosModelOdplist extends JModelItem {
	protected $dataModelo;
	
	public function getOrdenes(){
		$data 		 = JFactory::getApplication()->input->getArray();
		$idMutuo = $data['id'];

		$listado = getFromTimOne::getOrdenesPrestamo($idMutuo);

        return $listado;
	}
}

