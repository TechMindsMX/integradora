<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class AdminIntegradoraModelOdvform extends JModelList {

    public function __construct($config = array()) {
        $get = JFactory::getApplication()->input;
        $params = array('idOrden'=>'INT');
        $this->data = $get->getArray($params);
        parent::__construct($config);
    }

    public function getUserIntegrado(){
        $factura = new Integrado();
        $integrados = $factura->getIntegrados();

        return $integrados;
    }

    public function getSolicitud()
    {
        if (!isset($this->dataModelo)) {
            $this->dataModelo = new Integrado;
        }

        return $this->dataModelo;
    }

    public function getOrden($idOrden){
        return new \Integralib\OdVenta(null, $idOrden);
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados();

        return $integrados;
    }

    public function getIntegradoName($integardoId){
        $integrados = $this->getIntegrados();

        foreach ($integrados as $value) {
            if($value->integrado->integradoId == $integardoId){
                $return = $value->datos_personales->nom_comercial;
            }
        }
        return $return;
    }

    /**
     * @param $integradoId
     * @return IntegradoSimple
     */
    public function getIntegrado($integradoId){
        return new IntegradoSimple($integradoId);
    }
}
