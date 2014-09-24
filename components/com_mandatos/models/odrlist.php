<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para el listado de las Ordenes de Retiro para un integrado
 */
class MandatosModelOdrlist extends JModelItem {
    protected $dataModelo;

    public function getOrdenes($integradoId = null){
        $data 		 = JFactory::getApplication()->input->getArray();
        $integradoId = $data['integradoId'];

        $listado = getFromTimOne::getOrdenesCompra($integradoId);

        return $listado;
    }
}

