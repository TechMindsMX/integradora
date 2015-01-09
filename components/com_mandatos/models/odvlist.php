<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de las Ordenes de Deposito para un integrado
 */
class MandatosModelOdvlist extends JModelItem {

    function __construct(){
        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();

        parent::__construct();
    }
	
	public function getOrdenes($integradoId = null){
        $listado = getFromTimOne::getOrdenesVenta($integradoId);

        foreach ($listado as $key => $value) {
            $value->productos = json_decode($value->productos);
            $value->totalAmount = $this->getTotalAmount($value->productos);
        }

        return $listado;
    }

    public function getTotalAmount($productos){
        $totalAmount = 0;

        foreach ($productos as $producto) {
            $total = ($producto->cantidad*$producto->p_unitario);
            $montoIva = $total*($producto->iva/100);
            $montoIeps = $total*($producto->ieps/100);

            $totalAmount = $total+$montoIva+$montoIeps+$totalAmount;
        }

        return $totalAmount;
    }
}