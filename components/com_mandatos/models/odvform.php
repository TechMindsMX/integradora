<?php
use Integralib as v;

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class MandatosModelOdvform extends JModelItem {

    public function __construct(){
        $this->inputVars   = JFactory::getApplication()->input->getArray( array('idOrden' => 'INT') );
        $this->session     = JFactory::getSession();
        $this->catalogos   = new Catalogos();
        $this->integradoId = $this->session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    public function getOrden(){

        $orden = getFromTimOne::getOrdenesVenta($this->integradoId, $this->inputVars['idOrden']);

	    $orden = $this->calculateProductTotals($orden[0]);

        return $orden;
    }

	private function calculateProductTotals( $orden ) {
		foreach ( $orden->productosData as $key => $prod ) {
			$orden->productosData[$key]->subTotal = (float)$prod->cantidad * (float)$prod->p_unitario;
			$orden->productosData[$key]->total = $orden->productosData[$key]->subTotal * (1+( (float)$prod->iva /100 )) * (1+( (float)$prod->ieps /100 ));
		}

		return $orden;
	}

	public function getClientes(){
	    $cliente = new v\Cliente();
	    $clientes = $cliente->getAllActive($this->integradoId);

	    return $clientes;
    }

	public function getProyectos(){
        $proyectos = getFromTimOne::getActiveProyects($this->integradoId);

        return $proyectos;
    }

    public function getSubprojects(){
        $projects = $this->getProyectos();

        foreach ($projects as $proyect) {
	        $subprojects[$proyect->id_proyecto] = getFromTimOne::getActiveSubProyects($proyect->id_proyecto);
        }

        return $subprojects;
    }

	public function getestados(){
        $estados    = $this->catalogos->getEstados();

        return $estados;
    }

	public function getCatalogoIva(){
        return $this->catalogos->getCatalogoIVA();

    }

	public function getDatosSolicitud(){

        return new IntegradoSimple($this->integradoId);
    }

	public function getProductos(){
        $respuesta = array();

        $allproducts = getFromTimOne::getProducts($this->integradoId,null,1);

        foreach ($allproducts as $key => $value) {
            $datos = new stdClass();
            $datos->id_producto = $value->id_producto;
            $datos->productName = $value->productName;
            $respuesta[] = $datos;
        }

        return $respuesta;
    }

	public function getCuentas(){
		$dbq = JFactory::getDbo();
        $cuentas = getFromTimOne::selectDB('integrado_datos_bancarios', 'integrado_id = '. $dbq->quote($this->integradoId));
        foreach ($cuentas as $objeto) {
            $objeto->banco_cuenta_xxx = 'XXXXXX' . substr($objeto->banco_cuenta, -4, 4);
        }

        return $cuentas;
    }

}

