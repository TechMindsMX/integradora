<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelMutuoslist extends JModelItem {
    public function __construct(){
        $app 				= JFactory::getApplication();
        $post               = array('integradoId' => 'INT');
        $this->catalogos    = $this->get('catalogos');
        $this->data			= (object) $app->input->getArray($post);

        parent::__construct();
    }

    public function getPost(){
        return $this->data;
    }

    public function getTiposPago(){
        $tipos = getFromTimOne::getTiposPago();

        return $tipos;
    }

    public function getCatalogos() {
        $catalogos = new Catalogos;

        $catalogos->getBancos();

        return $catalogos;
    }

    public function getMutuosODP(){
        $integradoId   = $this->data->integradoId;
        $mutuos        = getFromTimOne::getMutuos($integradoId);
        $tiposPeriodos =  getFromTimOne::getTiposPago();
        foreach ($mutuos as $key => $value) {

            $value->tipoPeriodo = $tiposPeriodos[$value->paymentPeriod];
            $integradoAcredor   = new stdClass();
            $integradoDeudor    = new stdClass();

            $inAcredor = new IntegradoSimple($value->integradoIdE);
            $inDeudor  = new IntegradoSimple($value->integradoIdR);

            $inAcredor = $inAcredor->integrados[0];
            $inDeudor  = $inDeudor->integrados[0];

            if( is_null($inAcredor->datos_empresa) ){
                $datos_personales = $inAcredor->datos_personales;
                $integradoAcredor->nombre = is_null($datos_personales->nom_comercial)?$datos_personales->nombre_representante:$datos_personales->nom_comercial;
            }else{
                $integradoAcredor->nombre = $inAcredor->datos_empresa->razon_social;
            }
            $integradoAcredor->banco = $inAcredor->datos_bancarios;
            $value->integradoAcredor = $integradoAcredor;

            if( is_null($inDeudor->datos_empresa) ){
                $datos_personalesD = $inDeudor->datos_personales;
                $integradoDeudor->nombre = is_null($datos_personalesD->nom_comercial)?$datos_personalesD->nombre_representante:$datos_personalesD->nom_comercial;
            }else{
                $integradoDeudor->nombre = $inDeudor->datos_empresa->razon_social;
            }
            $integradoDeudor->banco  = $inDeudor->datos_bancarios;
            $value->integradoDeudor  = $integradoDeudor;
        }

        return $mutuos;
    }
}