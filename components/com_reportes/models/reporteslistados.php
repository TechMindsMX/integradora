<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class ReportesModelReporteslistados extends JModelItem {

    public function __construct(){
        $this->inputVars 		 = JFactory::getApplication()->input->getArray();
        $this->integradoId       = $this->inputVars['integradoId'];
        parent::__construct();
    }

    public function getOrden($integradoId = null){

    }

    public function getClientes(){
        $clientes = getFromTimOne::getClientes($this->integradoId);

        foreach($clientes as $key => $value){
            if($value->type === 0){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public function getProyectos(){
        $proyectos = getFromTimOne::getProyects($this->integradoId);
        $data = array();

        foreach($proyectos as $key => $value){
            if($value->parentId === 0){
                $data['proyectos'][] = $value;
                $parent = $value->name;
            }else{
                $data['subproyectos'][$value->parentId][]= $value;
            }
        }

        return $data;
    }

    public function getestados(){
        $catalogos  = new Catalogos();
        $estados    = $catalogos->getEstados();

        return $estados;
    }

    public function getDatosSolicitud(){
        $integrado = new Integrado();

        foreach($integrado->integrados as $key => $value){
            if($value->integrado_id === $this->integradoId){
                $data = $value;
            }
        }

        return $data;
    }

    public function getProductos(){
        $allproducts = getFromTimOne::getProducts($this->integradoId);

        return $allproducts;
    }
}

