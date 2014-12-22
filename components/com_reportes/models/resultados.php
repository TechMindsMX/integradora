<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelResultados extends JModelItem {

    protected $input;

    function __construct() {
        $post        = array('integradoId'=>'INT');
        $this->input = (object) JFactory::getApplication()->input->getArray($post);

        parent::__construct();
    }

    public function getCXC(){
        $integradoId   = $this->input->integradoId;
        $cxc           = getFromTimOne::getOrdersCxC($integradoId);
        $cxc           = $cxc->odv;

        return $cxc;
    }

    public function getCXP(){
        $integradoId = $this->input->integradoId;
        $cxp = getFromTimOne::getOrdersCxP($integradoId);
        $cxp = $cxp->odc;

        return $cxp;
    }

    public function getDataIntegrado(){
        $integrado = new IntegradoSimple($this->input->integradoId);

        return $integrado;
    }

    public function getReporte(){
        $input          = (object)JFactory::getApplication()->input->getArray(array('startDate'=>'string','endDate'=>'STRING', 'proyecto' => 'INT'));
        $startPeriod    = !is_null($input->startDate)?strtotime($input->startDate) : 1417392000;
        $endPeriod      = !is_null($input->endDate) ? strtotime($input->endDate) : 1420070399;

        $reporte = new stdClass();

        $reporte->period->startDate = $startPeriod;
        $reporte->period->endDate   = $endPeriod;

        getFromTimOne::convierteFechas($reporte->period);

        $reporte->totalIngresos = 0;
        $cxc = getFromTimOne::getOrdenesVenta($this->input->integradoId);
        $cxc = getFromTimOne::filterByDate($cxc, $startPeriod,$endPeriod);

        foreach ($cxc as $value) {
            if( ($value->status->id == 5) || ($value->status->id == 13) ) {
                if(is_null($input->proyecto)) {
                    $reporte->totalIngresos = (float)$reporte->totalIngresos + $value->totalAmount;
                }else{
                    if($value->projectId2 == 0){
                        if($value->projectId == $input->proyecto){
                            $reporte->totalIngresos = (float)$reporte->totalIngresos + $value->totalAmount;
                        }
                    }else{
                        if($value->projectId2 == $input->proyecto){
                            $reporte->totalIngresos = (float)$reporte->totalIngresos + $value->totalAmount;
                        }
                    }
                }
            }
        }

        $reporte->totalEgresos = 0;
        $cxp = getFromTimOne::getOrdenesCompra($this->input->integradoId);
        $cxp = getFromTimOne::filterByDate($cxp, $startPeriod,$endPeriod);

        foreach ($cxp as $value) {
            if( ($value->status->id == 5) || ($value->status->id == 13) ) {
                if( is_null($input->proyecto) ) {
                    $reporte->totalEgresos = (float)$reporte->totalEgresos + $value->totalAmount;
                }else{
                    if( isset($value->sub_proyecto) ){
                        if( (int)$value->sub_proyecto->id_proyecto == $input->proyecto ){
                            $reporte->totalEgresos = (float)$reporte->totalEgresos + $value->totalAmount;
                        }
                    }else{
                        if( (int)$value->proyecto->id_proyecto == $input->proyecto ){
                            $reporte->totalEgresos = (float)$reporte->totalEgresos + $value->totalAmount;
                        }
                    }
                }
            }
        }

        return $reporte;
    }

    public function getDetalleIngresos($periodStarDate = null, $periodEndDate = null){
        //Periodo diciembre (1417392000 primero de diciembre, 1420070399 31 de diciembre) 1418655600

        $input          = (object)JFactory::getApplication()->input->getArray(array('startDate'=>'string','endDate'=>'STRING', 'proyecto' => 'INT'));
        $startPeriod    = !is_null($input->startDate)?strtotime($input->startDate) : 1417392000;
        $endPeriod      = !is_null($input->endDate) ? strtotime($input->endDate) : 1420070399;
        $retorno        = array();
        $cxc            = $this->getCXC();
        $ordenesPagadas = getFromTimOne::getOrdenesVenta($this->input->integradoId);

        //Agregamos las ordenes autorizadas (CXC)
        foreach ($cxc as $value) {
            $integrado = new IntegradoSimple($value->clientId);

            if( !is_null($integrado->integrados[0]->datos_empresa) ) {
                $value->clientName = $integrado->integrados[0]->datos_empresa->razon_social;
            }else{
                $value->clientName = $integrado->integrados[0]->datos_personales->nom_comercial;
            }

            if(is_null($input->proyecto)) {
                $retorno[] = $value;
            }else{
                if($value->projectId2 == 0){
                    if($value->projectId == $input->proyecto){
                        $retorno[] = $value;
                    }
                }else{
                    if($value->projectId2 == $input->proyecto){
                        $retorno[] = $value;
                    }
                }
            }

        }

        //Agregamos las ordenes pagadas (Ingreso)
        foreach ($ordenesPagadas as $orden) {
            if($orden->status->id == 13){
                $integrado = new IntegradoSimple($orden->clientId);

                if( !is_null($integrado->integrados[0]->datos_empresa) ) {
                    $orden->clientName = $integrado->integrados[0]->datos_empresa->razon_social;
                }else{
                    $orden->clientName = $integrado->integrados[0]->datos_personales->nom_comercial;
                }
               // $retorno[] = $orden;
                if(is_null($input->proyecto)) {
                    $retorno[] = $orden;
                }else{
                    if($orden->projectId2 == 0){
                        if($orden->projectId == $input->proyecto){
                            $retorno[] = $orden;
                        }
                    }else{
                        if($orden->projectId2 == $input->proyecto){
                            $retorno[] = $orden;
                        }
                    }
                }
            }
        }

        $retorno = getFromTimOne::filterByDate($retorno,$startPeriod,$endPeriod);

        return $retorno;
    }

    public function getDetalleEgresos($periodStarDate = null, $periodEndDate = null){
        //Periodo diciembre (1417392000 primero de diciembre, 1420070399 31 de diciembre) 1418655600

        $input          = (object)JFactory::getApplication()->input->getArray(array('startDate'=>'string','endDate'=>'STRING', 'proyecto' => 'INT'));
        $startPeriod    = !is_null($input->startDate)?strtotime($input->startDate) : 1417392000;
        $endPeriod      = !is_null($input->endDate) ? strtotime($input->endDate) : 1420070399;
        $retorno        = array();
        $cxp            = $this->getCXP();
        $ordenesPagadas = getFromTimOne::getOrdenesCompra($this->input->integradoId);

        //Agregamos las ordenes autorizadas (CXP)
        foreach ($cxp as $value) {
            if(is_null($input->proyecto)) {
                $retorno[] = $value;
            }else{
                if((int)$value->proyecto->id_proyecto == $input->proyecto){
                    $retorno[] = $value;
                }
            }
        }

        //Agregamos las ordenes pagadas (Ingreso)
        foreach ($ordenesPagadas as $orden) {
            if($orden->status->id == 13){
                if(isset($input->proyecto)) {
                    $retorno[] = $orden;
                }else{
                    if( isset($orden->subproyecto) ) {
                        if ((int)$orden->proyecto->id_proyecto == $input->proyecto) {
                            $retorno[] = $orden;
                        }
                    }else{
                        if((int)$orden->sub_proyecto->id_proyecto == $input->proyecto){
                            $retorno[] = $orden;
                        }
                    }
                }
            }
        }

        $retorno = getFromTimOne::filterByDate($retorno,$startPeriod,$endPeriod);

        return $retorno;
    }

    public function getProyectos(){
        $integradoId = $this->input->integradoId;
        $proyectos = getFromTimOne::getProyects($integradoId);

        return $proyectos;
    }
}