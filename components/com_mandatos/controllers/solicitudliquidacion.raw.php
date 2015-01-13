<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllersolicitudliquidacion extends JControllerAdmin {

    protected $integradoId;

    function saveform() {
        $document       = JFactory::getDocument();
        $this->app 	    = JFactory::getApplication();
        $parametros     = array(
            'saldo'       => 'FLOAT',
            'monto'       => 'FLOAT'
        );
        $data           = $this->app->input->getArray($parametros);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $validacion     = new validador();
        $diccionario    = array('integradoId'   => array('tipo'=>'number', 'length' => '1'),
            'monto'         => array('tipo'=>'float', 'length' => '15'),
            'saldo'         => array('tipo'=>'float', 'length' => '15'));
        $valida = $validacion->procesamiento($data, $diccionario);
        $document->setMimeEncoding('application/json');

        foreach ($valida as $key => $value) {
            if(!is_bool($value)){
                echo json_encode($valida);
                return;
            }
        }
        $save = new sendToTimOne();
        $save->sendSolicitudLiquidacionTIMONE($data['monto'], $this->integradoId);

        $sesion = JFactory::getSession();
        $nuevoSaldo = $data['saldo'] - $data['monto'];
        $idTX = $sesion->get('idTx', 0,'solicitudliquidacion');

        $sesion->set('idTx',$idTX+1, 'solicitudliquidacion');
        $sesion->set('nuevoSaldo',$nuevoSaldo, 'solicitudliquidacion');

        $respuesta = array();
        $respuesta['nuevoSaldo']     = (FLOAT) $nuevoSaldo;
        $respuesta['nuevoSaldoText'] = number_format($nuevoSaldo,2);
        $respuesta['idTx']           = (INT) $idTX;
        $respuesta['success']        = true;


        if($respuesta['success']==true){
            $integrado              = new IntegradoSimple($this->integradoId);

            $contenido = JText::_('NOTIFICACIONES_9');
            $contenido = str_replace('$nombreIntegrado', '<strong style="color: #000000">'.$integrado->user->username.'</strong>',$contenido);
            $contenido = str_replace('$saldo', '<strong style="color: #000000">'.$respuesta['nuevoSaldo'].'</strong>',$contenido);
            $contenido = str_replace('$usuario', '<strong style="color: #000000">'.$integrado->user->username.'</strong>',$contenido);
            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);

            $data['titulo']         = JText::_('TITULO_9');
            $data['body']           = $contenido;

            $send                   = new Send_email();
            $send->notification($data);
        }
        echo json_encode($respuesta);
    }

    public function getDatosGeneracionFactura(){
        $integrados = getFromTimOne::getintegrados();
        $dataFactura = array();
        foreach ($integrados as $dataIntegrado) {
            $datosFactura = new stdClass();

            $datosFactura->receptor  = $dataIntegrado;
            $datosFactura->emisor    = 'Datos de Integradora';
            $dataconceptos = $this->getTXComisionesporServicios($dataIntegrado->integrado->integrado_id);

            $datosFactura->conceptos = $dataconceptos->conceptos;
            $datosFactura->subtotal = $dataconceptos->subtotal;
            $datosFactura->montoIva = $dataconceptos->montoIva;

            if(!is_null($dataconceptos->conceptos)) {
                $dataFactura[] = $datosFactura;
            }

        }

        var_dump($dataFactura);
        $create = new sendToTimOne();

        $create->generarFacturaComisiones($dataFactura);
        exit;
    }

    function getTXComisionesporServicios($integradoId){
        $txs = getFromTimOne::selectDB('txs_timone_mandato','idIntegrado = '.$integradoId);
        $subtotal = 0;
        $montoIva = 0;
        $conceptos = null;

        foreach ($txs as $tx) {
            $concepto = new stdClass();

            $numOrden = $this->getNumOrdenByType($tx);
            $monto = $this->getDataTxFromTIMONE($tx->idTx);
            $nombreComision  = getFromTimOne::getComisiones($tx->idComision);

            $concepto->descripcion = $nombreComision[0]->description.' '.JText::_(strtoupper($tx->tipoOrden).'_DESCRIPTION').' #'.$numOrden;
            $concepto->neto = ($monto->amount)/1.16;
            $concepto->iva = ($concepto->neto)*.16;

            $subtotal = $subtotal + $concepto->neto;
            $montoIva = $montoIva + $concepto->iva;

            $conceptos[] = $concepto;
        }
        $dataFacturaComisiones = new stdClass();

        $dataFacturaComisiones->conceptos = $conceptos;
        $dataFacturaComisiones->subtotal = $subtotal;
        $dataFacturaComisiones->montoIva = $montoIva;

        return $dataFacturaComisiones;

//        $subtotal = 0;
//        $impuestos = 0;
//        $total = 0;
//
//        $fechaInicial = new DateTime();
//        $fechaInicial->modify('first day of this month');
//
//        $fechaFinal = new DateTime();
//        $fechaFinal->modify('last day of this month');
//
//        $fechaI     = $fechaInicial->getTimestamp();
//        $fechaF     = $fechaFinal->getTimestamp();
//        $txsPeriodo = array();
//
//        foreach ($txs as $tx) {
//            $fechaTx = (INT)$tx->date;
//            if( ($fechaI <= $fechaTx) && ($fechaTx <= $fechaF) ) {
//                $integrado = new IntegradoSimple($tx->idIntegrado);
//
//                $tx->dataTx         = $this->getDataTxFromTIMONE($tx->idTx);
//                $tx->dataIntegrado  = $integrado->integrados;
//                $tx->dataOrden      = $this->getNumOrdenByType($tx->idOrden, $tx->tipoOrden);
//                $subtotal           = $subtotal + $tx->dataTx->amount;
//                $impuestos          = $impuestos+($subtotal*1.16);
//
//                $txsPeriodo[] = $tx;
//            }
//        }
//
//        $datosFactura->emisor = 'datos Integradora';
//        $datosFactura->subtotal = $subtotal;
//        $datosFactura->impuestos = $impuestos;
//
//        var_dump($datosFactura);
//        exit;
    }

    public function getDataTxFromTIMONE($idTx){
        $return = null;

        $objeto = new stdClass();

        $objeto->id     = (int)1;
        $objeto->date   = (int)1417478400;
        $objeto->amount = (float) 156.95;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)2;
        $objeto->date   = (int)1417478400;
        $objeto->amount = (float) 500;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)3;
        $objeto->date   = (int)1417737600;
        $objeto->amount = (float) 251.87;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)4;
        $objeto->date   = (int)1417910400;
        $objeto->amount = (float) 900;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)5;
        $objeto->date   = (int)1417910400;
        $objeto->amount = (float) 900;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)6;
        $objeto->date   = (int)1418064944;
        $objeto->amount = (float) 1500;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)7;
        $objeto->date   = (int)1418064944;
        $objeto->amount = (float) 1500;

        $dataTx[] = $objeto;

        $objeto = new stdClass();

        $objeto->id     = (int)8;
        $objeto->date   = (int)1417651200;
        $objeto->amount = (float) 893.95;

        $dataTx[] = $objeto;

        foreach ($dataTx as $tx) {
            if( $tx->id == $idTx ){
                $return = $tx;
            }
        }

        return $return;
    }

    public function getNumOrdenByType($txData){
        switch($txData->tipoOrden){
            case 'odc':
                $orden = getFromTimOne::getOrdenesCompra($txData->idIntegrado,$txData->idOrden);
                break;
            case 'odv':
                $orden = getFromTimOne::getOrdenesVenta($txData->idIntegrado,$txData->idOrden);
                break;
            case 'odd':
                $orden = getFromTimOne::getOrdenesDeposito($txData->idIntegrado,$txData->idOrden);
                break;
            case 'odr':
                $orden = getFromTimOne::getOrdenesRetiro($txData->idIntegrado,$txData->idOrden);
                break;
        }

        return $orden[0]->numOrden;
    }
}
