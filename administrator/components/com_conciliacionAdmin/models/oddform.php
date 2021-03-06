<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class conciliacionadminModelOddform extends JModelList {

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

    public function getOrden(){
        $idOrden = $this->data['idOrden'];

        $data = getFromTimOne::getOrdenesDeposito(null,$idOrden);

        foreach($data as $value){
            $value->integradoName = $this->getIntegradoName($value->integradoId);
        }
        return $data[0];
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

    public function getTransacciones($integradoId=null){
        $orden = $this->getOrden();
        $respuesta = getFromTimOne::getTxIntegradoSinMandato();
        foreach ($respuesta as $tx) {
            if( ( ($orden->integradoId == $tx->integradoId) || ($tx->integradoId == 0) ) && $tx->conciliacionMandato == 0 ) {
                $return[] = $tx;
            }
        }

        return $return;
    }
}
