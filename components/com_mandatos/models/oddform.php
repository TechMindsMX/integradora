<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.imagenes');

//Modelo de datos para formulario para crear una Orden de Deposito dado un integrado
class MandatosModelOddform extends JModelItem {
    protected $dataModelo;

    public function getOrden($integradoId = null){
        $data 		    = JFactory::getApplication()->input->getArray();
        $integradoId    = $data['integradoId'];
        $oddNum         = isset($data['oddnum'])?$data['oddnum']:null;
        $listado        = getFromTimOne::getOrdenesDeposito($integradoId, $oddNum);

        return $listado;
    }
}