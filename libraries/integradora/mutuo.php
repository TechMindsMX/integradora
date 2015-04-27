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

        foreach ($mutuos as $key => $mutuo) {
            $integradoAcredor = new stdClass();
            $integradoDeudor  = new stdClass();

            $this->mutuo = $mutuo;

            $tipo = $tipos[$mutuo->paymentPeriod];
            $mutuo->tipoPeriodo = $tipo->nombre;
            $mutuo->duracion    = $mutuo->quantityPayments/$tipo->periodosAnio;

            $integradoEmisor = new IntegradoSimple($mutuo->integradoIdE);
            $integradoReceptor = new IntegradoSimple($mutuo->integradoIdR);

            if (is_null($integradoEmisor->integrados[0]->datos_empresa)) {
                $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_personales->nom_comercial;
                $integradoAcredor->rfc = $integradoEmisor->integrados[0]->datos_personales->rfc;
            } else {
                $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_empresa->razon_social;
                $integradoAcredor->rfc = $integradoEmisor->integrados[0]->datos_empresa->rfc;
            }

            if (is_null($integradoReceptor->integrados[0]->datos_empresa)) {
                if (is_null($integradoReceptor->integrados[0]->datos_personales->nom_comercial)) {
                    $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nombre_representante;
                    $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_personales->rfc;
                } else {
                    $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nom_comercial;
                    $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_personales->rfc;
                }
            } else {
                $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_empresa->razon_social;
                $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_empresa->rfc;
            }

            $integradoAcredor->datosBancarios = $integradoEmisor->integrados[0]->datos_bancarios;
            $integradoDeudor->datosBancarios = $integradoReceptor->integrados[0]->datos_bancarios;

            $mutuo->integradoAcredor = $integradoAcredor;
            $mutuo->integradoDeudor = $integradoDeudor;

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

        $mutuo->totalAmount = $mutuo->totalInteres + $mutuo->totalIva + $mutuo->totalCapital;
    }

    protected function getSaldoMutuo(){
        $mutuo = $this->mutuo;
        $odps = getFromTimOne::getOrdenesPrestamo($mutuo->id);
        $mutuo->saldo = $mutuo->totalAmount;

        foreach ($odps as $key => $odp) {
            if($key != 0) {
                if ($odp->status == Integralib\OrdenFn::getStatusIdByName('Pagada')) {
                    $mutuo->saldo = $mutuo->saldo - $odp->intereses - $odp->iva_intereses - $odp->capital;
                }
            }
        }

    }
}