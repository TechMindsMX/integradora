<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');
jimport('integradora.mutuo');

class MandatosModelMutuoslist extends JModelItem {
    public function __construct(){
        $session            = JFactory::getSession();
        $app 				= JFactory::getApplication();
        $post               = array('layout' => 'string');
        $this->catalogos    = $this->get('catalogos');
        $this->data			= (object) $app->input->getArray($post);
        $this->data->integradoId = $session->get('integradoId',null,'integrado');

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

    public function getMutuosAcreedor(){
        $allMutuos = getFromTimOne::getParametrosMutuo();
        $mutuosAcredor = array();

        foreach ($allMutuos as $value) {
            if($this->data->integradoId == $value->integradoIdE){
                $value->status = getFromTimOne::getOrderStatusName($value->status);
                $auths = getFromTimOne::getOrdenAuths($value->id, 'mutuo_auth');
                $value->integradoHasAuth = getFromTimOne::checkUserAuth($auths);
                $mutuosAcredor[] = $value;

            }
        }
        $classMutuo = new mutuo();
        $mutuosAcredor = $classMutuo->formatData($mutuosAcredor);

        return $mutuosAcredor;
    }

    public function getMutuosdeudor(){
        $allMutuos = getFromTimOne::getParametrosMutuo();
        $mutuosDeudor = array();

        foreach ($allMutuos as $value) {
            if($this->data->integradoId == $value->integradoIdR){
                $value->status = getFromTimOne::getOrderStatusName($value->status);
                $auths = getFromTimOne::getOrdenAuths($value->id, 'mutuo_auth');
                $value->integradoHasAuth = getFromTimOne::checkUserAuth($auths);
                $mutuosDeudor[] = $value;
            }
        }
        $classMutuo = new mutuo();
        $mutuosDeudor = $classMutuo->formatData($mutuosDeudor);

        return $mutuosDeudor;
    }
}