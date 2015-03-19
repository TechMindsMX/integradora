<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.integrado');
jimport('integradora.gettimone');

/**
 * Methods supporting a list of Facturas records.
 */
class AdminintegradoraModelOddlist extends JModelList {

    public function __construct($config = array()) {

        parent::__construct($config);
    }

    public function getUserIntegrado(){

       $factura = new Integrado();
       $integrados = $factura->getIntegrados();

       return $integrados;
    }

    public function getSolicitud($integradoId = null)
    {
        if (!isset($this->dataModelo)) {
            $this->dataModelo = new Integrado;
        }

        return $this->dataModelo;
    }

    public function getOrdenes(){
        $data = getFromTimOne::getOrdenesDeposito();

        $data = getFromTimOne::filterOrdersByStatus($data,array(3,5));

        foreach($data as $value){
            $value->integradoName = $this->getIntegradoName($value->integradoId);
        }
        return $data;
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados();

        return $integrados;
    }

    public function getIntegradoName($integardoId){
        $integrados = $this->getIntegrados();

        foreach ($integrados as $value) {
            if($value->integrado->integrado_id == $integardoId){
                $return = $value->datos_personales->nom_comercial;
            }
        }
        return $return;
    }
}
