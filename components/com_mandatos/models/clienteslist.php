<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelClienteslist extends JModelItem {
	protected $dataModelo;
	
	public function getclientes($integradoId = null){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];
		
		if (!isset($this->dataModelo)) {
			$this->dataModelo = getFromTimOne::getClientes($integradoId);
		}

		return $this->dataModelo;
	}
}

