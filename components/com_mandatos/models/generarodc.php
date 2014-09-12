<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelGenerarodc extends JModelItem {
	protected $dataModelo;
	
	public function getOrden($integradoId = null){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];
		
		if (!isset($this->dataModelo)) {
			$this->dataModelo = getFromTimOne::getOrdenesCompra($integradoId);
		}

		return $this->dataModelo;
	}
	
	public function getProyectos(){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];
		
		$proyectos = getFromTimOne::getProyects($integradoId);
		
		return $proyectos;
	}
	
	public function getProviders(){
		$data 		 = JFactory::getApplication()->input->getArray();
		$integradoId = $data['integradoId'];
		$proveedores = array();
		
		$clientes = getFromTimOne::getClientes($integradoId);
		
		foreach ($clientes as $key => $value) {
			if($value->type == 1){
				$proveedores[] = $value;
			}
		}
		
		return $proveedores;
	}
}

