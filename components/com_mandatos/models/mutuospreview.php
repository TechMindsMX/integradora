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
            'integradoId'       => 'INT',
            'idMutuo'           => 'INT'
        );
        $this->inputVars 		 = JFactory::getApplication()->input->getArray($post);


		parent::__construct();
	}

    public function getDataPost(){
        return $this->inputVars;
    }

    public function getMutuo(){
        $data = getFromTimOne::getMutuos(null,$this->inputVars['idMutuo']);
        $tipos = getFromTimOne::getTiposPago();

        $data = $data[0];

        $integradoEmisor   = new IntegradoSimple($data->integradoIdE);
        $integradoReceptor = new IntegradoSimple($data->integradoIdR);

        if(is_null($integradoEmisor->integrados[0]->datos_empresa)){
            $nombreEmisor = $integradoEmisor->integrados[0]->datos_personales->nom_comercial;
        }else{
            $nombreEmisor = $integradoEmisor->integrados[0]->datos_empresa->razon_social;
        }

        if(is_null($integradoReceptor->integrados[0]->datos_empresa)){
            $nombreReceptor = $integradoReceptor->integrados[0]->datos_personales->nom_comercial;
        }else{
            $nombreReceptor = $integradoReceptor->integrados[0]->datos_empresa->razon_social;
        }

        $data->integradoEmisor   = $nombreEmisor;
        $data->integradoReceptor = $nombreReceptor;

        $data->paymentPeriod = $tipos[$data->paymentPeriod];

        return $data;
	}

}

