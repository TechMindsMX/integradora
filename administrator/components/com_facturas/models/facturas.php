<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.catalogos');
jimport('integradora.integrado');
jimport('integradora.gettimone');
jimport('integradora.xmlparser');

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
        $data = getFromTimOne::getFacturasComision();
        $dataFacturas = array();

        foreach ($data as $factura) {
            $this->getdataFactura($factura);
            $fechaHr                 = explode('T',$factura->factura->comprobante['FECHA']);
            $fechaHr[0]              = str_replace('-','/',$fechaHr[0]);
            $fechaNumero             = strtotime($fechaHr[0]);
            $fecha                   = date('d-m-Y',$fechaNumero);

            $respuesta               = new stdClass();
            $nombreEmisor            = $factura->factura->emisor['attrs']['NOMBRE'];
            $iva                     = $factura->factura->impuestos->iva->importe;
            $respuesta->id           = (INT) $factura->id;
            $respuesta->integradoId  = (INT) $factura->integradoId;
            $respuesta->status       = (INT) $factura->status;
            $respuesta->fecha        = $fecha;
            $respuesta->fechaNum     =  $fechaNumero;
            $respuesta->folio        = (INT) $factura->factura->comprobante['FOLIO'];
            $respuesta->emisor       = $nombreEmisor;
            $respuesta->iva          = $iva;
            $respuesta->subtotal     = (FLOAT) $factura->factura->comprobante['SUBTOTAL'];
            $respuesta->total        = (FLOAT) $factura->factura->comprobante['TOTAL'];
            $dataFacturas[]          = $respuesta;
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
}
