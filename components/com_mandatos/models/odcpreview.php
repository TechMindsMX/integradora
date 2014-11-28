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
			$this->odc = getFromTimOne::getOrdenesCompra($this->inputVars['integradoId'], $this->inputVars['odcnum']);
		}

		// Verifica si la ODC exite para el integrado o redirecciona
		if (is_null($this->odc)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODC_INVALID'), 'error');
		}
		
		$this->getProyectFromId($this->odc->proyecto);
		
		$this->getProviderFromID($this->odc->proveedor);
        exit;

		return $this->odc;
	}
	
	public function getProyectFromId($proyId){
		$proyecto = getFromTimOne::getProyects($this->inputVars['integradoId'], $proyId);

        var_dump($proyecto);
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

    public function getdataFactura($objeto){
        $urlXML = $objeto->urlXML;

        $xmlFileData  = file_get_contents($urlXML);
        $manejadorXML = new xml2Array();
        $datos 		  = $manejadorXML->manejaXML($xmlFileData);

        $objeto->impuestos = $datos->impuestos->totalTrasladados;

        //tomo los productos de la factura
        foreach ($datos->conceptos as $value) {
            $objeto->productos[] = $value;
        }

        foreach ($datos->impuestos as $key => $value) {
            if($key == 'iva'){
                $objeto->iva = $value;
            }elseif($key == 'ieps'){
                $objeto->ieps = $value;
            }
        }

        return $datos;
    }
}

