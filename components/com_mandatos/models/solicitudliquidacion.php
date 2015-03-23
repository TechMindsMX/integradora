<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelSolicitudliquidacion extends JModelItem
{
    protected $dataModelo;
	protected $integradoId;

	/**
	 * @param mixed $integradoId
	 */
	public function setIntegradoId( $integradoId ) {
		$this->integradoId = $integradoId;
	}

	public function getIntegrado() {
		$integ = new IntegradoSimple($this->integradoId);

		$integ->getTimOneData();

		return $integ;
	}

    public function getSaldoOperaciones($operaciones){
		return getFromTimOne::getSaldoOperacionesPorLiquidar($operaciones);
    }

    public function getOperaciones(){
	    return getFromTimOne::getOperacionesPorLiquidar($this->integradoId);
    }
}

