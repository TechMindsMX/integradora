<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class AdminIntegradoraModelOddform extends JModelList {

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
            $integrado = $this->getIntegrado($value->integradoId);
            $value->integradoName = $integrado->getDisplayName();
        }

        return $data[0];
    }

    /**
     * @param $integradoId
     * @return IntegradoSimple
     */
    public function getIntegrado($integradoId){
        return new IntegradoSimple($integradoId);
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
}
