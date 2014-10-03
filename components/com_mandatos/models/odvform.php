<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class MandatosModelOdvform extends JModelItem {

    public function __construct(){
        $this->inputVars 		 = JFactory::getApplication()->input->getArray();
        $this->integradoId       = $this->inputVars['integradoId'];
        parent::__construct();
    }

    public function getOrden($integradoId = null){
    }
}

