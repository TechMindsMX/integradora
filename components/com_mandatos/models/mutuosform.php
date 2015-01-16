<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelMutuosform extends JModelItem {

    function __construct(){
        $session                      = JFactory::getSession();
        $app                          = JFactory::getApplication();
        $post                         = array(
            'id'                => 'INT',
            'integradoId'       => 'INT',
            'integradoIdR'      => 'INT',
            'paymentPeriod'     => 'INT',
            'cuotaOcapital'     => 'INT',
            'quantityPayments'  => 'FLOAT',
            'totalAmount'       => 'FLOAT',
            'interes'           => 'FLOAT',
            'beneficiario'      => 'STRING',
            'rfc'               => 'STRING',
            'layout'            => 'STRING',
            'banco_codigo'      => 'STRING',
            'banco_cuenta'      => 'STRING',
            'banco_sucursal'    => 'STRING',
            'banco_clabe'       => 'STRING'
        );
        $this->inputData              = (object)$app->input->getArray($post);
        $integradoId                  = $session->get('integradoId', null, 'integrado');
        $this->inputData->integradoId = is_null($integradoId)?$this->inputData->integradoId:$integradoId;

        parent::__construct();
    }

    public function getInputData(){
        return $this->inputData;
    }

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
        $tablas = getFromTimOne::getTablaAmotizacion((object)$dataTabla);

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

        return $mutuo;
    }
}