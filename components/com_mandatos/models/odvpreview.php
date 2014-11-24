<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdvpreview extends JModelItem {

	public $odv;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();

		parent::__construct();
	}

	public function getOrdenes(){

		if (!isset($odvs)) {
			$odvs = getFromTimOne::getOrdenesVenta($this->inputVars['integradoId']);
		}

		foreach ($odvs as $key => $value) {
			$subTotalOrden = 0;
			if ($value->idOdv == $this->inputVars['odvnum'] ) {
                $value->productos = json_decode($value->productos);
				foreach ($value->productos  as $producto ) {
					$subTotalOrden = $subTotalOrden + $producto->cantidad * $producto->p_unitario;
				}
				$value->totalAmount = $subTotalOrden;
				$this->odv = $value;
			}
		}
        $this->odv->iva = .16;
        $this->odv->ieps = 0;
        $this->odv->observaciones  = '';

		// Verifica si la ODV exite para el integrado o redirecciona
		if (is_null($this->odv)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->inputVars['integradoId']), JText::_('ODV_INVALID'), 'error');
		}

		$this->getProyectFromId($this->odv->projectId);

		$this->getClientFromID($this->odv->clientId);

		return $this->odv;
	}
	public function getProyectFromId($proyId){
		$proyKeyId = array();

		$proyectos = getFromTimOne::getProyects($this->inputVars['integradoId']);

			// datos del proyecto y subproyecto involucrrado
		foreach ( $proyectos as $key => $proy) {
			$proyKeyId[$proy->id_proyecto] = $proy;
		}

		if(array_key_exists($proyId, $proyKeyId)) {
			$this->odv->proyecto = $proyKeyId[$proyId];

			if($this->odv->proyecto->parentId > 0) {
				$this->odv->sub_proyecto	= $this->odv->proyecto;
				$this->odv->proyecto		= $proyKeyId[$this->odv->proyecto->parentId];
			} else {
				$this->odv->subproyecto 	= null;
			}
		}
	}

	public function getClientFromID($providerId){
		$proveedores = array();

		$clientes = getFromTimOne::getClientes($this->inputVars['integradoId']);

		foreach ($clientes as $key => $value) {
			if($value->type == 0){
				$proveedores[$value->id] = $value;
			}
		}

		$this->odv->proveedor = $proveedores[$providerId];
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}

}