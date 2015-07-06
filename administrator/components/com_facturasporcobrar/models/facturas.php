<?php

/**
 * @version     1.0.0
 * @package     com_facturasporcobrar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Nestor Ismael Aguilar Estrada <aguilar_2001@hotmail.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.view');
jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

/**
 * Methods supporting a list of Facturasporcobrar records.
 */
class FacturasporcobrarModelFacturas extends JModelList {

    public function __construct($config = array()) {

        parent::__construct($config);
        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

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

    public function getFacturas($integradoId = null){

        $returnDataFactura = array();
        $data = getFromTimOne::getOrdenesVenta($integradoId);

        foreach ($data as $key => $value) {
            if( ($value->status->id == 5) || ($value->status->id == 8) ) {
                $value->comision = 0;
                $comsionesFactura = getFromTimOne::selectDB('txs_mandatos', 'orderType = "CCom-odv"');

                foreach($comsionesFactura as $comision){
                    if($value->id == $comision->idOrden){
                        $value->comision = $comision->amount;
                    }
                }
                $returnDataFactura[] = $value;
            }
        }
        return $returnDataFactura;
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados(50);

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
            if($value->integrado->integradoId == $integardoId){
                $return = $value->datos_personales->nom_comercial;
            }
        }
        return $return;
    }

}
