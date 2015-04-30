<?php

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

/**
 * Modelo de datos para Listados de Reportes
 */
class ReportesModelReporteslistados extends JModelItem {

    public function __construct(){

        $sesion = JFactory::getSession();
        $this->integradoId = $sesion->get('integradoId', null, 'integrado');

        parent::__construct();
    }

    public function getSolicitud()
    {
        if (!isset($this->dataModelo)) {
            $this->dataModelo = new IntegradoSimple($this->integradoId);
        }
        return $this->dataModelo;
    }

    public function getBalanceList (){
//        $list = \Integralib\ReportBalance::getIntegradoExistingBalanceList( $this->integradoId );

//        return $list;
    }

    public function getflujo (){
    }

    public function getresultados (){
        $resultados = getFromTimOne::getResultados($this->integradoId);
        return $resultados;
    }

	public function getProjects() {
		return getFromTimOne::getActiveProyects($this->integradoId);
	}
}

