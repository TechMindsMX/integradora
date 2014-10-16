<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listados de Reportes
 */
class ReportesModelReporteslistados extends JModelItem {

    public function getSolicitud($integradoId = null)
    {
        if (!isset($this->dataModelo)) {
            $this->dataModelo = new Integrado;
        }
        return $this->dataModelo;
    }

    public function __construct(){
        $this->inputVars 		 = JFactory::getApplication()->input->getArray();
        $this->integradoId       = $this->inputVars['integradoId'];
        parent::__construct();
    }

    public function getBalance (){
        $balance = getFromTimOne::getBalances($this->integradoId);
        return $balance;
    }
}

