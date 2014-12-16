<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelMutuosform extends JModelItem {
    public function getTiposPago(){
        $tipos = getFromTimOne::getTiposPago();

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
}