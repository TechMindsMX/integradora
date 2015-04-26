<?php
use Integralib\ReportResultados;

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

	    $this->input            = (object) JFactory::getApplication()->input->getArray( array (
		                                                                                    'startDate'   => 'STRING',
		                                                                                    'endDate'     => 'STRING',
		                                                                                    'project'     => 'INT'
	                                                                                    ) );
	    $this->input->startDate   = ! is_null( $this->input->startDate ) ? strtotime( $this->input->startDate ) : null;
	    $this->input->endDate     = ! is_null( $this->input->endDate ) ? strtotime( $this->input->endDate ) : null;

	    $session = JFactory::getSession();
	    $this->input->integradoId = $session->get('integradoId', null, 'integrado');

	    parent::__construct();
    }

	/**
	 * @return ReportResultados
	 */
	public function getReporte(){

		$reportResultados      = new ReportResultados($this->input->integradoId , $this->input->startDate, $this->input->endDate, $this->input->project);
		$reportResultados->calculateIngresos();
		$reportResultados->calculateEgresos();
		$reportResultados->startPeriod = $reportResultados->getFechaInicio();
		$reportResultados->endPeriod   = $reportResultados->getFechaFin();

		return $reportResultados;
	}

/*	public function getCXC(){
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
    }*/

/*    public function getDetalleIngresos($periodStarDate = null, $periodEndDate = null){

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

            if(is_null($this->input->proyecto)) {
                $retorno[] = $value;
            }else{
                if($value->projectId2 == 0){
                    if($value->projectId == $this->input->proyecto){
                        $retorno[] = $value;
                    }
                }else{
                    if($value->projectId2 == $this->input->proyecto){
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
                if(is_null($this->input->proyecto)) {
                    $retorno[] = $orden;
                }else{
                    if($orden->projectId2 == 0){
                        if($orden->projectId == $this->input->proyecto){
                            $retorno[] = $orden;
                        }
                    }else{
                        if($orden->projectId2 == $this->input->proyecto){
                            $retorno[] = $orden;
                        }
                    }
                }
            }
        }

        $retorno = getFromTimOne::filterByDate($retorno, $this->input->startPeriod, $this->input->endPeriod);

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
    }*/

}