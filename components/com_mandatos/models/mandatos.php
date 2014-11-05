<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class MandatosModelMandatos extends JModelItem {
	
	protected $dataModelo;
	
	public function getIntegrados(){
        $response = array();
		if (!isset($this->dataModelo)) {
			$this->dataModelo = new Integrado;
		}

        foreach ($this->dataModelo->integrados as $value) {
            if($value->integrado_principal == 1 && $value->integrado_permission_level >= 2){
                $integrado = new stdClass();
                $integrado->integradoId = $value->integrado_id;
                $integrado->name = $value->datos_personales->nom_comercial;
                $response[] = $integrado;
            }
        }

        return $response;
	}
	
	public function getCatalogos() {
		$catalogos = new Catalogos;
		
		$catalogos->getNacionalidades();
		$catalogos->getEstados();
		$catalogos->getBancos();
		
		return $catalogos;
	}
}

