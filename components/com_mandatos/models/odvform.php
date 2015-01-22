<?php
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
        $session           = JFactory::getSession();
        $this->catalogos   = new Catalogos();
        $this->integradoId = $session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    public function getOrden(){

        $orden = getFromTimOne::getOrdenesVenta($this->integradoId, $this->inputVars['idOrden']);

        return $orden[0];
    }

    public function getClientes(){
        $clientes = getFromTimOne::getClientes($this->integradoId, 0);

        return $clientes;
    }

    public function getProyectos(){
        $proyectos = getFromTimOne::getProyects($this->integradoId);
        $data = array();

        foreach($proyectos as $key => $value){
            if($value->parentId == 0){
                $data['proyectos'][] = $value;
                $parent = $value->name;
            }else{
                $data['subproyectos'][$value->parentId][]= $value;
            }
        }

        return $data;
    }

    public function getestados(){
        $estados    = $this->catalogos->getEstados();

        return $estados;
    }

    public function getCatalogoIva(){
        return $this->catalogos->getCatalogoIVA();

    }

    public function getDatosSolicitud(){
        $integrado = new Integrado();

        foreach($integrado->integrados as $key => $value){
            if($value->integrado_id == $this->integradoId){
                $data = $value;
            }
        }

        return $data;
    }

    public function getProductos(){
        $respuesta = array();
        $datos = new stdClass();

        $allproducts = getFromTimOne::getProducts($this->integradoId);

        foreach ($allproducts as $key => $value) {
            $datos = new stdClass();
            $datos->id_producto = $value->id_producto;
            $datos->productName = $value->productName;
            $respuesta[] = $datos;
        }

        return $respuesta;
    }

    public function getCuentas(){
        $cuentas = getFromTimOne::selectDB('integrado_datos_bancarios', 'integrado_id = '.$this->integradoId);
        foreach ($cuentas as $objeto) {
            $objeto->banco_cuenta_xxx = 'XXXXXX' . substr($objeto->banco_cuenta, -4, 4);
        }

        return $cuentas;
    }

}

