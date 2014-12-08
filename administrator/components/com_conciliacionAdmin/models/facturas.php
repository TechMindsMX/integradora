<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.catalogos');
jimport('integradora.integrado');
jimport('integradora.gettimone');
jimport('integradora.xmlparser');

class conciliacionadminModelFacturas extends JModelList {

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
        $data = getFromTimOne::getFacturasComision();
        $dataFacturas = array();

        foreach ($data as $factura) {
            $factura->url_xml = $factura->urlXML;
            unset($factura->urlXML);

            $factura->factura = getFromTimOne::getDataFactura($factura);
            $fechaHr          = explode('T',$factura->factura->comprobante['FECHA']);
            $fechaHr[0]       = str_replace('-','/',$fechaHr[0]);
            $fechaNumero      = strtotime($fechaHr[0]);
            $fecha            = date('d-m-Y',$fechaNumero);

            $integradoName            = $this->getIntegradoName($factura->integradoId);
            $respuesta                = new stdClass();
            $nombreEmisor             = $factura->factura->receptor['attrs']['NOMBRE'];
            $iva                      = $factura->factura->impuestos->iva->importe;
            $respuesta->id            = (INT) $factura->id;
            $respuesta->integradoId   = (INT) $factura->integradoId;
            $respuesta->integradoName = $integradoName;
            $respuesta->status        = (INT) $factura->status;
            $respuesta->fecha         = $fecha;
            $respuesta->fechaNum      =
            $respuesta->folio         = (INT) $factura->factura->comprobante['FOLIO'];
            $respuesta->emisor        = $nombreEmisor;
            $respuesta->iva           = $iva;
            $respuesta->subtotal      = (FLOAT) $factura->factura->comprobante['SUBTOTAL'];
            $respuesta->total         = (FLOAT) $factura->factura->comprobante['TOTAL'];
            $dataFacturas[]           = $respuesta;
        }

        return $dataFacturas;
    }

    public  function getComision(){
        $data = getFromTimOne::getComisiones();

        foreach ($data as $comision) {
            if($comision->description === 'Factura'){
                $respuesta = $comision->monto;
            }
        }
        return $respuesta;
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados();

        return $integrados;
    }

    public function getdataFactura($factura){
        $urlXML       = $factura->urlFactura;
        $xmlFileData  = file_get_contents('../'.$urlXML);
        $manejadorXML = new xml2Array();
        $datos 		  = $manejadorXML->manejaXML($xmlFileData);

        $factura->factura = $datos;
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
