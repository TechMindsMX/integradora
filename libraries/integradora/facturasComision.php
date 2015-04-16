<?php

/**
 * Created by PhpStorm.
 * User: lutek-tim
 * Date: 09/01/2015
 * Time: 03:07 PM
 * @property mixed integradoId
 */

use Integralib\OdVenta;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.rutas');
jimport('integradora.xmlparser');
jimport('integradora.integrado');

class facturasComision extends OdVenta{
	public $status;
	public $ieps;
	public $paymentMethod;
	public $placeIssue;
	public $conditions;
	public $productosData;
	public $urlXML;

	public function generateFact($integradoId){
        $db                   = JFactory::getDbo();

        $save                 = new sendToTimOne();
        $respuesta            = false;

        $this->emisor         = new IntegradoSimple(1);
        $this->receptor       = new IntegradoSimple($integradoId);
        $this->conditions     = 1;
        $this->urlXML         = null;
        $this->status         = 0;
        $this->ieps           = 0;
        $this->paymentMethod  = $this->getpaymentMethod();
        $this->placeIssue     = $this->getplaceIssue();
        $this->productosData  = $this->getProductsFromTxComision($integradoId);

        if( !empty($this->productosData) ) {
            $this->iva  = $this->getIvaComision($this->productosData);
            $factObj                    = $save->generaObjetoFactura($this);

            if ($factObj != false) {
                $fecha = new DateTime();
                $xmlFactura = $save->generateFacturaFromTimone($factObj);
                $factComDB = new stdClass();

	            $factComDB->integradoId = $integradoId;
	            $factComDB->status      = 0;
	            $factComDB->urlXML      = $save->saveXMLFile($xmlFactura);
	            $factComDB->createdDate = $fecha->getTimestamp();
	
	            $db->transactionStart();

                try {
                    $db->insertObject('#__facturas_comisiones', $factComDB);

	                $db->transactionCommit();

	                $this->id = $db->insertid();

	                $respuesta = $factObj;

                } catch (Exception $e) {
                    $db->transactionRollback();
                }
            }
        }

        return $respuesta;
    }

    public function getpaymentMethod(){
        $paymentMethodObj = new stdClass();

        $paymentMethodObj->id   = '1';
        $paymentMethodObj->name = 'Transferencia Interbancaria';

        return $paymentMethodObj;
    }

    public function getplaceIssue(){
        $placeIssure = new stdClass();
        $placeIssure->id = '9';
        $placeIssure->clave = '09';
        $placeIssure->nombre = 'Distrito Federal';
        $placeIssure->abrev = 'DF';

        return $placeIssure;
    }

    public function getProductsFromTxComision($integradoId){
        $catalogo = new Catalogos();
        $iva = $catalogo->getFullIva();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from('#__txs_mandatos AS txm')
            ->join('LEFT','#__txs_timone_mandato AS txtm on txtm.id = txm.id')
            ->where('txm.orderType = "CCom" AND txtm.idIntegrado = '.$integradoId);

        try{
            $db->setQuery($query);
            $productos = $db->loadObjectList();
        }catch (Exception $e){
            $productos = array();
        }

        foreach ($productos as $key => $value) {

            $producto = new stdClass();
            $detalle = getFromTimOne::getTxDataByTxId($value->idTx);
            $detalle = json_decode($detalle->data);

            $producto->name = 'Comision Orden de compra';
            $producto->descripcion = 'Comision en la Fecha '.date('d-m-Y',$value->date);
            $producto->cantidad = '1' ;
            $producto->unidad = 'no Aplica';
            $producto->p_unitario = $detalle->amount / (1+($iva/100));
            $producto->iva = $iva;
            $producto->ieps = '0';

            $respuesta[] = $producto;
        }

        return $respuesta;
    }

    private function getIvaComision($productos){
//        $totalIva    = 0;

        foreach ($productos as $producto) {
            $iva          = (INT) $producto->iva;
            $subTotalProd = ($producto->p_unitario * $producto->cantidad);

            $totalIva[] = $subTotalProd * ($iva/100);
        }

        $total = array_sum($totalIva);
        return  $total;
    }

    public function getFactComision(){
        $allFacturas = getFromTimOne::selectDB('facturas_comisiones');

        foreach ($allFacturas as $factura) {
            $parseXML = new xml2Array();
            $xml = file_get_contents('../'.$factura->urlXML);
            $xmlParsed = $parseXML->manejaXML($xml);

            $factura->detalleFact = $xmlParsed;
        }
        return $allFacturas;
    }
}