<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de las Ordenes de Deposito para un integrado
 */
class MandatosModelOdrform extends JModelItem {

    protected $dataOrden;

    function __construct(){
        $this->data 		= JFactory::getApplication()->input->getArray();
        $this->integradoId  = $this->data['integradoId'];
        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();

        parent::__construct();
    }

    public function getOrdenes($integradoId = null){
        $dataOrden = getFromTimOne::getOrdenesRetiro($this->integradoId);

	    return $dataOrden;
    }

	public function getBalance( ){
		$balance = 150;
		return $balance;
	}
}
?>