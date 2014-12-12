<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelMutuosform extends JModelItem {
    public function getTipoPago(){
        $tipo = array(
            2 => 'Quincenal',
            3 => 'Mensual',
            4 => 'Bimestral',
            5 => 'Trimestral',
            6 => 'Semestral',
            7 => 'Anual'
        );

        return $tipo;
    }

    public function getCatalogos() {
        $catalogos = new Catalogos;

        $catalogos->getNacionalidades();
        $catalogos->getEstados();
        $catalogos->getBancos();

        return $catalogos;
    }
}