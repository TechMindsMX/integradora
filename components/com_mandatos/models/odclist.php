<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

/**
 * Modelo de datos para Listado de las Ordenes de Deposito para un integrado
 */
class MandatosModelOddlist extends JModelItem {
	protected $dataModelo;

    function __construct(){
        $this->data 		= JFactory::getApplication()->input->getArray();
        $this->integradoId  = $data['integradoId'];
        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();
    }
	
	public function getDeposito($integradoId = null){
        var_dump($this);exit;
	}
}

