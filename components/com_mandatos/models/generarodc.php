<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.xmlparser');

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
	
	public function getdata2xml(){
		move_uploaded_file($_FILES['factura']['tmp_name'], "media/archivosJoomla/".$_FILES['factura']['name']);
		$xmlFileData    = file_get_contents("media/archivosJoomla/".$_FILES['factura']['name']);
		$data 			= new xml2Array();
		$datos 			= $data->manejaXML($xmlFileData);
		
		return $datos;
	}
}

