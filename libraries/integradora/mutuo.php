<?php
/**
 * Created by PhpStorm.
 * User: Lutek
 * Date: 19/02/2015
 * Time: 10:43 AM
 */

defined('JPATH_PLATFORM') or die;
jimport('integradora.catalogos');
jimport('integradora.gettimone');

class mutuo {
    public $mutuo = '';

    public function formatData($allData){
        $mutuos        = $allData;
        $tiposPeriodos =  new Catalogos();
        $tipos = $tiposPeriodos->getTiposPeriodos();

        foreach ($mutuos as $key => $value) {
            $this->mutuo = $value;

            $tipo = $tipos[$value->paymentPeriod];
            $value->tipoPeriodo = $tipo->nombre;
            $value->duracion    = $value->quantityPayments/$tipo->periodosAnio;

            $integradoAcredor   = new stdClass();
            $integradoDeudor    = new stdClass();

            $inAcredor = new IntegradoSimple($value->integradoIdE);
            $inDeudor  = new IntegradoSimple($value->integradoIdR);

            $inAcredor = $inAcredor->integrados[0];
            $inDeudor  = $inDeudor->integrados[0];

            if( is_null($inAcredor->datos_empresa) ){
                $datos_personales = $inAcredor->datos_personales;
                $integradoAcredor->nombre = is_null($datos_personales->nom_comercial)?$datos_personales->nombre_representante:$datos_personales->nom_comercial;
            }else{
                $integradoAcredor->nombre = $inAcredor->datos_empresa->razon_social;
            }

            $integradoAcredor->banco = $inAcredor->datos_bancarios;
            $value->integradoAcredor = $integradoAcredor;

            if( is_null($inDeudor->datos_empresa) ){
                $datos_personalesD = $inDeudor->datos_personales;
                $integradoDeudor->nombre = is_null($datos_personalesD->nom_comercial)?$datos_personalesD->nombre_representante:$datos_personalesD->nom_comercial;
            }else{
                $integradoDeudor->nombre = $inDeudor->datos_empresa->razon_social;
            }

            $integradoDeudor->banco  = $inDeudor->datos_bancarios;
            $value->integradoDeudor  = $integradoDeudor;

            $this->operaciones();
            $this->getSaldoMutuo();
        }

        return $mutuos;
    }

    protected function operaciones(){
        $mutuo = $this->mutuo;
        $tablaAmortizacion = json_decode($mutuo->jsonTabla);

        $tabla   = array();
        $mutuo->totalCapital = 0;
        $mutuo->totalIva     = 0;
        $mutuo->totalInteres = 0;

        if( isset($tablaAmortizacion->amortizacion_capital_fijo) ){
            $tabla = $tablaAmortizacion->amortizacion_capital_fijo;
        }elseif( isset($tablaAmortizacion->amortizacion_cuota_fija) ){
            $tabla = $tablaAmortizacion->amortizacion_cuota_fija;
        }

        foreach($tabla as $value){
            $mutuo->totalCapital = $value->acapital + $mutuo->totalCapital;
            $mutuo->totalIva     = $value->iva + $mutuo->totalIva;
            $mutuo->totalInteres = $value->intereses + $mutuo->totalInteres;
        }

        $mutuo->realTotalAmount = $mutuo->totalInteres + $mutuo->totalIva + $mutuo->totalCapital;
    }

    protected function getSaldoMutuo(){
        $mutuo = $this->mutuo;
        $odps = getFromTimOne::getOrdenesPrestamo($mutuo->id);
        $mutuo->saldo = $mutuo->realTotalAmount;

        foreach ($odps as $key => $odp) {
            if($key != 0) {
                if ($odp->status == Order::getStatusIdByName('Pagada')) {
                    $mutuo->saldo = $mutuo->saldo - $odp->intereses - $odp->iva_intereses - $odp->capital;
                }
            }
        }

    }
}