<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class MandatosModelGenerarodc extends JModelItem {
	protected $dataModelo;

    public function __construct(){
        $this->inputVars 		 = JFactory::getApplication()->input->getArray();
        $this->integradoId       = $this->inputVars['integradoId'];
        parent::__construct();
    }

	public function getOrden($integradoId = null){
		if (!isset($this->dataModelo)) {
			$this->dataModelo = getFromTimOne::getOrdenesCompra($this->integradoId);
		}

		return $this->dataModelo;
	}
	
	public function getProyectos(){
		$proyectos = getFromTimOne::getProyects($this->integradoId);
		
		return $proyectos;
	}
	
	public function getProviders(){
		$proveedores = array();
		
		$clientes = getFromTimOne::getClientes($this->integradoId);
		
		foreach ($clientes as $key => $value) {
			if($value->type == 1){
				$proveedores[] = $value;
			}
		}
		
		return $proveedores;
	}
}

