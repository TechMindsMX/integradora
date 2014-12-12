<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelMutuospreview extends JModelItem {


	public function __construct(){
        $post               = array(
            'integradoId'    => 'INT',
            'idMutuo'        => 'INT',
            'integradoIdR'   => 'INT',
            'beneficiario'   => 'STRING',
            'rfc'            => 'STRING',
            'layout'         => 'STRING',
            'expirationDate' => 'FLOAT',
            'payments'       => 'FLOAT',
            'totalAmount'    => 'FLOAT',
            'interes'        => 'FLOAT'
        );
        $this->inputVars 		 = JFactory::getApplication()->input->getArray($post);

		
		parent::__construct();
	}

    public function getDataPost(){
        return $this->inputVars;
    }

    public function getDataIntegrados(){

	}
}

