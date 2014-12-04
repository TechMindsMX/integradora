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
class FacturasModelFacturas extends JModelList {

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



    public function getFacturas(){
        $data = getFromTimOne::getFactura();
        foreach($data as $value){
            $strlimpia = str_replace('T',' ',$value->Comprobante->fecha);
            $array = explode(' ',$strlimpia);
            $strFH = strtotime($array[0]);
            $date   = date('d-m-Y',$strFH);
            $value->Comprobante->fechaFormateada = $date;
            $value->Comprobante->fechaNumero = $strFH;
        }

        return $data;
    }

    public  function getComision(){
        $data = getFromTimOne::getComisiones();
        return $data;
    }
}
