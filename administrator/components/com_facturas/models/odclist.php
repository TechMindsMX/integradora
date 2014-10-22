<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

/**
 * Methods supporting a list of Facturas records.
 */
class FacturasModelOdclist extends JModelList {

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
        $data = getFromTimOne::getOrdenesCompra();
        $usuarios = $this->getUserIntegrado();

        foreach($data as $value){
            foreach($usuarios as $usuario){
                if($usuario->integrado_id == $value->integradoId){
                    $value->integradoName = $usuario->name;
                }
            }
        }
        return $data;
    }
}
