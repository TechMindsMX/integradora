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
    public function getIntegrados(){
        $integradosArray = Integrado::getActiveIntegrados();

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
        $integradora = new IntegradoSimple(1);

        return $integradora->integrados[0]->datos_bancarios;
    }
}