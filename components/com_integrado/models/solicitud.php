<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class IntegradoModelSolicitud extends JModelItem {
	
	protected $dataModelo;
	
	public function getSolicitud($integradoId = null)
	{
		if (!isset($this->dataModelo)) {
			$this->dataModelo = new Integrado;
			
			$ruta = new IntRoute;
			$this->dataModelo->action = $ruta->getUrl('integrado','solicitud');
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

