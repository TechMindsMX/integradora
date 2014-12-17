<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelMutuosform extends JModelItem {
    public function getTiposPago(){
        $catalogos = new Catalogos();

        $tipos = $catalogos->getTiposPeriodos();

        return $tipos;
    }

    public function getCatalogos() {
        $catalogos = new Catalogos;

        $catalogos->getNacionalidades();
        $catalogos->getEstados();
        $catalogos->getBancos();

        return $catalogos;
    }

    public static function getTablaAmortizacion($data){
        $respuesta = new stdClass();

        $dataTabla   = array(
            'tiempoplazo' => $data->quantityPayments,
            'tipoPlazo'   => $data->paymentPeriod,
            'capital'     => $data->totalAmount,
            'interes'     => $data->interes
        );
        $tablas = getFromTimOne::getTabla((object)$dataTabla);

            if($data->cuotaOcapital === 0){
                $respuesta->intereses_con_iva          = $tablas->intereses_con_iva;
                $respuesta->capital                    = $tablas->capital;
                $respuesta->tipoPeriodos               = $tablas->tipoPeriodos;
                $respuesta->tperiodo                   = $tablas->tperiodo;
                $respuesta->periodos_year              = $tablas->periodos_year;
                $respuesta->tasa_periodo               = $tablas->tasa_periodo;
                $respuesta->tasa_efectiva_periodo      = $tablas->tasa_efectiva_periodo;
                $respuesta->capital_fija               = $tablas->capital_fija;
                $respuesta->amortizacion_capital_fijo  = $tablas->amortizacion_capital_fijo;

            }elseif($data->cuotaOcapital === 1){
                $respuesta->intereses_con_iva          = $tablas->intereses_con_iva;
                $respuesta->capital                    = $tablas->capital;
                $respuesta->tipoPeriodos               = $tablas->tipoPeriodos;
                $respuesta->tperiodo                   = $tablas->tperiodo;
                $respuesta->periodos_year              = $tablas->periodos_year;
                $respuesta->tasa_periodo               = $tablas->tasa_periodo;
                $respuesta->tasa_efectiva_periodo      = $tablas->tasa_efectiva_periodo;
                $respuesta->factor                     = $tablas->factor;
                $respuesta->cuota_Fija                 = $tablas->cuota_Fija;
                $respuesta->amortizacion_cuota_fija    = $tablas->amortizacion_cuota_fija;
            }

        return json_encode($respuesta);
    }

    public static function getMutuo($idMutuo){
        $mutuo = getFromTimOne::getMutuos(null, $idMutuo);
        $mutuo = $mutuo[0];

        $integradoDeudor  = new IntegradoSimple($mutuo->integradoIdR);
        $integradoDeudor = $integradoDeudor->integrados[0];

        if( is_null($integradoDeudor->datos_empresa) ){
            $datosPersonales = $integradoDeudor->datos_personales;
            $nombre = is_null($datosPersonales->nom_comercial)?$datosPersonales->nombre_representante:$datosPersonales->nom_comercial;
        }else{
            $datosEmpresa = $integradoDeudor->datos_empresa;
            $nombre = $datosEmpresa->razon_social;
        }
        $mutuo->rfc = $integradoDeudor->datos_personales->rfc;
        $mutuo->beneficiario = $nombre;

        $mutuo->banco_codigo   = !is_null($integradoDeudor->datos_bancarios)?$integradoDeudor->datos_bancarios->banco_codigo  :null;
        $mutuo->banco_cuenta   = !is_null($integradoDeudor->datos_bancarios)?$integradoDeudor->datos_bancarios->banco_cuenta  :null;
        $mutuo->banco_sucursal = !is_null($integradoDeudor->datos_bancarios)?$integradoDeudor->datos_bancarios->banco_sucursal:null;
        $mutuo->banco_clabe    = !is_null($integradoDeudor->datos_bancarios)?$integradoDeudor->datos_bancarios->banco_clabe   :null;

        return $mutuo;
    }
}