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
        $session                      = JFactory::getSession();
        $post                         = array('idMutuo'=> 'INT');
        $this->inputVars 		      = (object)JFactory::getApplication()->input->getArray($post);
        $this->inputVars->integradoId = $session->get('integradoId', null, 'integrado');

		parent::__construct();
	}

    public function getDataPost(){
        return $this->inputVars;
    }

    public function getMutuo(){
        $data = getFromTimOne::getMutuos(null,$this->inputVars->idMutuo);
        $tipos = getFromTimOne::getTiposPago();
        $integradoAcredor = new stdClass();
        $integradoDeudor  = new stdClass();


        $data = $data[0];
        $integradoEmisor   = new IntegradoSimple($data->integradoIdE);
        $integradoReceptor = new IntegradoSimple($data->integradoIdR);

        if(is_null($integradoEmisor->integrados[0]->datos_empresa)){
            $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_personales->nom_comercial;
        }else{
            $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_empresa->razon_social;
        }

        if(is_null($integradoReceptor->integrados[0]->datos_empresa)){
            if(is_null($integradoReceptor->integrados[0]->datos_personales->nom_comercial)){
                $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nombre_representante;
            }else {
                $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nom_comercial;
            }
        }else{
            $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_empresa->razon_social;
        }

        $integradoAcredor->datosBancarios = $integradoEmisor->integrados[0]->datos_bancarios;
        $integradoDeudor->datosBancarios  = $integradoReceptor->integrados[0]->datos_bancarios;

        $data->integradoAcredor   = $integradoAcredor;
        $data->integradoDeudor    = $integradoDeudor;

        $data->paymentPeriod = $tipos[$data->paymentPeriod];

        return $data;
	}

    public function getIntegrado()	{
        return new IntegradoSimple($this->inputVars->integradoId);
    }
}

