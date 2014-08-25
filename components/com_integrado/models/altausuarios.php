<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class IntegradoModelAltausuarios extends JModelItem {
	
	protected $dataModelo;
	
	public function getUsuarios($integradoId = null)
	{
		if (!isset($this->dataModelo)) {
			$this->dataModelo = new Integrado;
			$integrado = new ReflectionClass('integradoSimple');
			
			if (count($this->dataModelo->integrados) == 0) {
				return false;
			}
			$this->dataModelo = $integrado->newInstance($this->dataModelo->integrados[0]->integrado_id);
			
		}

		return $this->dataModelo;
	}
	
	public function getCatalogos() {
		$catalogos = new Catalogos;
		
		$catalogos->getNacionalidades();
		$catalogos->getEstados();
		$catalogos->getBancos();
		
		return $catalogos;
	}
}

