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
	    $integradoId = JFactory::getSession()->get('integradoId', null, 'integrado');

        $this->data 		= JFactory::getApplication()->input->getArray();
        $this->integradoId  = isset($integradoId) ? $integradoId : $this->data['integradoId'];
	    $this->idOrden      = isset($this->data['idOrden']) ? $this->data['idOrden'] : null;
        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();

        parent::__construct();
    }

    public function getOrdenes(){
        $this->dataOrden = getFromTimOne::getOrdenesRetiro($this->integradoId, $this->idOrden);

	    if(isset($this->idOrden)){
		    $this->verifyStatusEditable();
	    }

	    return $this->dataOrden[0];
    }

	public function getBalance( ){
		$balance = 150;
		return $balance;
	}

	private function verifyStatusEditable() {
		if($this->dataOrden[0]->status > 0){
			$url = 'index.php?option=com_mandatos&view=odrlist&integradoId='.$this->integradoId;
			$msg = 'ORDEN_CON_ESTATUS_NO_EDITABLE';
			JFactory::getApplication()->redirect($url, $msg, 'error');
		}
	}
}
?>