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

class ConciliacionBancoModelDetalle extends JModelLegacy {
    public function getIntegrados(){
        $integradosArray = getFromTimOne::getintegrados();

        return $integradosArray;
    }

    public function getcatalogoBancos(){
        $catalogos = new Catalogos();

        $bancos = $catalogos->getBancos();

        foreach ($bancos as $objBanco) {
            $bancoArray[$objBanco->claveClabe] = $objBanco->banco;
        }

        return $bancoArray;
    }
}