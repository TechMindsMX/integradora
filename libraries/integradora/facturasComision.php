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

    public function getFacturaComision($integradoId){
        $save = new sendToTimOne();

        $datosFacturaComision = new OdVenta();
        $datosFacturaComision->emisor = new IntegradoSimple(1);
        $datosFacturaComision->receptor = new IntegradoSimple($integradoId);
        $datosFacturaComision->paymentMethod = $this->getpaymentMethod();
        $datosFacturaComision->conditions = 1;
        $datosFacturaComision->placeIssue = $this->getplaceIssue();
        $datosFacturaComision->urlXML = null;
        $datosFacturaComision->status = 0;
        $datosFacturaComision->productosData = $this->getProductosFact($integradoId);
        $datosFacturaComision->iva = $this->getIvaComision($datosFacturaComision->productosData);
        $datosFacturaComision->ieps = 0;

        $factObj = $save->generaObjetoFactura( $datosFacturaComision );

        if ( $factObj != false ) {
            $xmlFactura = $save->generateFacturaFromTimone( $factObj );
        }

        return $xmlFactura;
    }

    public function getProveedor(){
        $objProveedor = new stdClass();
        $objProveedor->id = 4;
        $objProveedor->type = 0;
        $objProveedor->integrado_id = 1;
        $objProveedor->status = 0;
        $objProveedor->rfc = 'LUT031214C01';
        $objProveedor->tradeName = 'CLiente 1';
        $objProveedor->corporateName = 'Cliente 1 S.A. de C.V.';
        $objProveedor->contact = 'Luis Enrique Magaña Manzano';
        $objProveedor->phone = '1111111111';

        $bancos = array();
        $bancoObj = new stdClass();
        $bancoObj->datosBan_id      = '5';
        $bancoObj->integrado_id     = '7';
        $bancoObj->banco_codigo     = '002';
        $bancoObj->banco_cuenta     = '4977002575';
        $bancoObj->banco_sucursal   = '497';
        $bancoObj->banco_clabe      = '002180497700257529';
        $bancoObj->banco_file       = 'media/archivosJoomla/7_db_banco_file.jpg';
        $bancoObj->banco_cuenta_xxx = 'XXXXXX2575';
        $bancoObj->banco_clabe_xxx  = 'XXXXXXXXXXXXXX7529';
        $bancos[] = $bancoObj;
        $objProveedor->bancos = $bancos;

        return $objProveedor;
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

//Obtiene todas las txs de comision y se ordenan como productos para generar la factura
    public function getProductosFact($integradoId){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from('#__txs_mandatos AS txm')
            ->join('LEFT','#__txs_timone_mandato AS txtm on txtm.id = txm.id')
            ->where('txm.orderType = "CCom"');

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
}