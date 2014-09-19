<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

//Modelo de datos para formulario para crear una Orden de Deposito dado un integrado
class MandatosModelOddform extends JModelItem {
    protected $dataModelo;

    public function getOrden($integradoId = null){
        $integrado 	 = new Integrado;
        $currUser	 = Jfactory::getUser();
        $data 		 = JFactory::getApplication()->input->getArray();
        $integradoId = $data['integradoId'];
        $oddNum      = isset($data['oddnum'])?$data['oddnum']:null;
        $datos       = null;
        $listado = getFromTimOne::getOrdenesCompra($integradoId);

        if(!is_null($oddNum)){
            foreach($listado as $key => $value){
                if($oddNum == $value->id){
                    $datos = $value;
                }
            }
        }

        return $datos;
    }
}