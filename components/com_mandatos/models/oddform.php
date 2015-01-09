<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('integradora.rutas');

//Modelo de datos para formulario para crear una Orden de Deposito dado un integrado
class MandatosModelOddform extends JModelItem {

    public function getOrden($integradoId = null){
        $data 		    = JFactory::getApplication()->input->getArray( array('idOrden' => 'INT') );

        $session = JFactory::getSession();
        $integradoId    = $session->get('integradoId', null, 'integrado');

        $oddId          = isset($data['idOrden']) ? $data['idOrden'] : null;
        $listado        = getFromTimOne::getOrdenesDeposito($integradoId, $oddId);

        return $listado;
    }
}