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
    public    $integradoId;

    function __construct()
    {
        $filtro = array('integradoId' => 'NUMBER');
        $data = JFactory::getApplication()->input->getArray($filtro);
        $this->integradoId = $data['integradoId'];

        parent::__construct();
    }

    public function getSaldo(){
        $saldos = getFromTimOne::getSaldoOperacionesPorLiquidar($this->integradoId);

        return $saldos;
    }

    public function getOperaciones(){
        $operaciones = getFromTimOne::getOperacionesPorLiquidar($this->integradoId);

        return $operaciones;
    }
}

