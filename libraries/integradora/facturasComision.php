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
    private $db;

    public function __construct(){
        $this->db = JFactory::getDbo();
    }

    public function generateFact($integradoId){
        $save                 = new sendToTimOne();
        $respuesta            = false;
        $datosFacturaComision = new OdVenta();

        $datosFacturaComision->emisor         = new IntegradoSimple(1);
        $datosFacturaComision->receptor       = new IntegradoSimple($integradoId);
        $datosFacturaComision->conditions     = 1;
        $datosFacturaComision->urlXML         = null;
        $datosFacturaComision->status         = 0;
        $datosFacturaComision->ieps           = 0;
        $datosFacturaComision->paymentMethod  = $this->getpaymentMethod();
        $datosFacturaComision->placeIssue     = $this->getplaceIssue();
        $datosFacturaComision->productosData  = $this->getProductsFromTxComision($integradoId);
        $datosFacturaComision->iva            = $this->getIvaComision($datosFacturaComision->productosData);

        $factObj = $save->generaObjetoFactura( $datosFacturaComision );

        if ( $factObj != false ) {
            $fecha      = new DateTime();
            $xmlFactura = $save->generateFacturaFromTimone( $factObj );
            $factComDB  = new stdClass();

            $factComDB->integradoId = $integradoId;
            $factComDB->status      = 0;
            $factComDB->urlXML      = $save->saveXMLFile($xmlFactura);
            $factComDB->createdDate = $fecha->getTimestamp();

            $this->db->transactionStart();

            try{
                $this->db->insertObject('#__facturas_comisiones',$factComDB);

                $respuesta = true;

                $this->db->transactionCommit();
            }catch (Exception $e){
                $this->db->transactionRollback();
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
            $producto->p_unitario = $detalle->amount;
            $producto->iva = '16';
            $producto->ieps = '0';

            $respuesta[] = $producto;
        }

        return $respuesta;
    }

    private function getIvaComision($productos){
        $totalAmount = 0;
        $totalIva    = 0;
        $totalIeps   = 0;

        foreach ($productos as $producto) {
            $iva          = (INT) $producto->iva;
            $subTotalProd = ($producto->p_unitario*$producto->cantidad);

            $totalAmount = $totalAmount + $subTotalProd;
            $totalIva = $totalIva + ($subTotalProd/$iva);
        }

        return $totalIva;
    }

    private function getFactComision(){

    }
}