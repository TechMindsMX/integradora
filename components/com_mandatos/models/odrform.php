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

	    $this->session      = JFactory::getSession();
	    $this->integradoId  = $this->session->get( 'integradoId', null, 'integrado' );

	    $this->data 		= JFactory::getApplication()->input->getArray();
	    $this->idOrden      = isset($this->data['idOrden']) ? $this->data['idOrden'] : null;

        parent::__construct();
    }

    public function getOrdenes(){
        $orden = getFromTimOne::getOrdenesRetiro(null, $this->idOrden);
		$this->dataOrden = $orden[0];

	    if(isset($this->idOrden)){
		    $this->verifyStatusEditable();
	    }

	    return $this->dataOrden;
    }

	public function getBalance( ){
        $integrado = new IntegradoSimple($this->integradoId);
		$integrado->getTimOneData();

        return $integrado->timoneData->balance;
	}

	private function verifyStatusEditable() {
		if($this->dataOrden->status->id != 1){
			$url = 'index.php?option=com_mandatos&view=odrlist&integradoId='.$this->integradoId;
			$msg = 'ORDEN_CON_ESTATUS_NO_EDITABLE';
			JFactory::getApplication()->redirect($url, $msg, 'error');
		}
	}
}