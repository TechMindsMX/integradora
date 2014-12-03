<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 22-Oct-14
 * Time: 3:57 PM
 */

jimport('integradora.gettimone');
jimport('integradora.integrado');

class ConciliacionBancoModelDetalle extends JModelLegacy {
    public function getIntegrados(){

        $integrados = getFromTimOne::getintegradosIds();

        var_dump($integrados);
    }
}