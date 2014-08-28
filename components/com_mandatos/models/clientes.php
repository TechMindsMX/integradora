<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelClientes extends JModelItem {
	protected $dataModelo;
	
	public function getclientes($integradoId = null){
		$integradoId = getFromTimOne::getIntegradoId(JFactory::getUser()->id);
		
		if (!isset($this->dataModelo)) {
			$this->dataModelo = getFromTimOne::getClientes($integradoId['integrado_id']);
		}

		return $this->dataModelo;
	}
}

