<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 22-Oct-14
 * Time: 3:57 PM
 */

jimport('integradora.gettimone');
jimport('integradora.integrado');
jimport('integradora.catalogos');

class AdminintegradoraModelConciliacionBancoForm extends JModelLegacy {

    function __construct(){
        $this->integradora = new \Integralib\Integrado();

        parent::__construct();
    }

    public function getIntegrados(){
        $integrados = Integrado::getActiveIntegrados();
        $integradosArray = array();

        foreach ($integrados as $value) {

            if($this->integradora->getIntegradoraUuid() != $value->integrado->integradoId){
                $integradosArray[] = $value;
            }
        }

        return $integradosArray;
    }

    public function getcatalogoBancos() {
        $catalogos = new Catalogos();

        $bancos = $catalogos->getBancos();

        foreach ( $bancos as $objBanco ) {
            $bancoArray[ $objBanco->claveClabe ] = $objBanco->banco;
        }

        return $bancoArray;
    }

    public function getBancosIntegradora(){
        $this->integradora->getIntegradora();

        return $this->integradora->integrado->integrados[0]->datos_bancarios;
    }
}