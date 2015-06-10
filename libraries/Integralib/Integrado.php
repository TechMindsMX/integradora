<?php
/**
 * Created by PhpStorm.
 * User: lutek
 * Date: 25/05/2015
 * Time: 04:39 PM
 */

namespace Integralib;


class Integrado {

    public $integrado;
    public $integradoraUuid = INTEGRADORA_UUID;

    public function setInstance(\IntegradoSimple $integradoSimple){
        $this->integrado = $integradoSimple;
    }

    public function isIntegradora(){
        return $this->integrado->getId() == $this->integradoraUuid;
    }

    public function getIntegradoraUuid(){
        return $this->integradoraUuid;
    }

    public function getIntegradora(){
        $this->setInstance(new \IntegradoSimple($this->integradoraUuid));
    }
}