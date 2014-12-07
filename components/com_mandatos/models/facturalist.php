<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelFacturalist extends JModelItem {
	protected $dataModelo;

	public function getFacturas($integradoId = null){

		$data 		 = JFactory::getApplication()->input->getArray();

		$integradoId = $data['integradoId'];

		$listado = getFromTimOne::getFacturasVenta($integradoId);

        $clientes = getFromTimOne::getClientes($integradoId);

        foreach ($listado as $k => $v) {
            foreach ($clientes as $key => $value) {
                if($value->id == $v->clientId){
                    $v->clientName = $value->tradeName;
                }
            }

        }

        return $listado;
	}
}

