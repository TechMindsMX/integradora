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
	
	public function getClientes( $integradoId ){

		if (!isset($this->dataModelo)) {
			$this->dataModelo = getFromTimOne::getClientes($integradoId);
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

