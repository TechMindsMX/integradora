<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de ordenes de prestamo de un integrado, dado un id de mutuo
 */
class MandatosModelOdplist extends JModelItem {
    public function __construct(){
        $session                 = JFactory::getSession(); //Se establece la variable de sesión de Joomla
        $app 				     = JFactory::getApplication();
        $post                    = array('id' => 'INT');
        $this->data			     = (object) $app->input->getArray($post);
        $this->data->integradoId = $session->get('integradoId',null,'integrado');

        parent::__construct();
    }

    public function getInputData(){
        return $this->data;
    }
	
	public function getOrdenes(){
		$idMutuo = $this->data->id;

		$listado = getFromTimOne::getOrdenesPrestamo($idMutuo);

        return $listado;
	}

    public function getDataMutuo(){
        $mutuo = getFromTimOne::getMutuos(null,$this->data->id);

        return $mutuo[0];
    }
}

