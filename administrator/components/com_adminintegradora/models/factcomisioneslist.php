<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');
jimport('integradora.facturasComision');

class AdminintegradoraModelFactcomisioneslist extends JModelList {

    public function __construct($config = array()) {

        parent::__construct($config);
    }

    public function getFacturas(){
        $facturasComision = new facturasComision();

        return $facturasComision->getFactComision();
    }
}
