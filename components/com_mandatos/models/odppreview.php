<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para la vista previa de ordenes de prestamo
 */
class MandatosModelodppreview extends JModelItem {


	public function __construct(){
        $post               = array(
            'integradoId'       => 'INT',
            'id'                => 'INT'
        );
        $this->inputVars 		 = JFactory::getApplication()->input->getArray($post);

		parent::__construct();
	}

    public function getDataPost(){
        return $this->inputVars;
    }

    public function getOrden(){
        $orden = getFromTimOne::getOrdenesPrestamo(null,$this->inputVars['id']);
        $orden = $orden[0];

        $orden->datosMutuo = getFromTimOne::getMutuos(null,$orden->idMutuo);
        $orden->datosMutuo = $orden->datosMutuo[0];

        return $orden;
    }
}

