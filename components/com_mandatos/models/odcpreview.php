<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelOdcpreview extends JModelItem {

	public $odc;
	
	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();
		
		parent::__construct();
	}
	
	public function getOrdenes(){

		if (!isset($odcs)) {
			$odcs = getFromTimOne::getOrdenesCompra($this->inputVars['integradoId']);
		}
		
		foreach ($odcs as $key => $value) {
			if ($value->id == $this->inputVars['odcnum'] ) {
				$this->odc = $value;
			}
		}
		// Verifica si la ODC exite para el integrado o redirecciona
		if (is_null($this->odc)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODC_INVALID'), 'error');
		}
		
		$this->getProyectFromId($this->odc->proyecto);
		
		$this->getProviderFromID($this->odc->proveedor);
		
		return $this->odc;
	}
	
	public function getProyectFromId($proyId){
		$proyKeyId = array();
		
		$proyectos = getFromTimOne::getProyects($this->inputVars['integradoId']);
		
		// datos del proyecto y subproyecto involucrrado
		foreach ( $proyectos as $key => $proy) {
			$proyKeyId[$proy->id] = $proy;
		}
			
		if(array_key_exists($proyId, $proyKeyId)) {
			$this->odc->proyecto = $proyKeyId[$proyId];
			
			if($this->odc->proyecto->parentId > 0) {
				$this->odc->sub_proyecto	= $this->odc->proyecto;
				$this->odc->proyecto		= $proyKeyId[$this->odc->proyecto->parentId];
			} else {
				$this->odc->subproyecto 	= null;
			}
		}
	}
	
	public function getProviderFromID($providerId){
		$proveedores = array();
		
		$clientes = getFromTimOne::getClientes($this->inputVars['integradoId']);
		
		foreach ($clientes as $key => $value) {
			if($value->type == 1){
				$proveedores[$value->id] = $value;
			}
		}
		
		$this->odc->proveedor = $proveedores[$providerId];
	}
	
	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}
}

