<?php

/**
 * Created by PhpStorm.
 * User: lutek-tim
 * Date: 09/01/2015
 * Time: 03:07 PM
 * @property mixed integradoId
 */

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.rutas');
jimport('integradora.xmlparser');
jimport('integradora.integrado');

class facturasComision {

    public function getFacturaComision($integradoId){
        $integrados = new Integrado();
        $save = new sendToTimOne();

        $datosFacturaComision = new stdClass();

        $datosFacturaComision->integradoId = 1;
        $datosFacturaComision->clientId = 4;
        $datosFacturaComision->paymentMethod = $this->getpaymentMethod();
        $datosFacturaComision->conditions = 1;
        $datosFacturaComision->placeIssue = $this->getplaceIssue();
        $datosFacturaComision->urlXML = null;
        $datosFacturaComision->status = 0;
        $datosFacturaComision->productosData = $this->getProductosFact($integradoId);
        $datosFacturaComision->proveedor = $this->getProveedor();

        $factObj = $save->generaObjetoFactura( $datosFacturaComision );

        /*if ( $factObj != false ) {
            $xmlFactura = $save->generateFacturaFromTimone( $factObj );

            var_dump($xmlFactura);
        }*/
var_dump($factObj);
        exit;
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

        $paymentMethodObj->id   = '3';
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
        $productos = getFromTimOne::selectDB('txs_timone_mandato','idIntegrado = '.$integradoId);


        foreach ($productos as $key => $value) {
            $producto = new stdClass();
            $detalle = getFromTimOne::getTxDataByTxId($value->idTx);

            $producto->name = 'Comision Orden de compra';
            $producto->descripcion = 'Comision por la '.$value->tipoOrden.' numero '.$key.' en la Fecha '.date('d-m-Y',$detalle->date);
            $producto->cantidad = '1' ;
            $producto->unidad = 'no Aplica';
            $producto->p_unitario = $detalle->amount;
            $producto->iva = '16';
            $producto->ieps = '0';

            $respuesta[] = $producto;
        }

        return $respuesta;
    }
}