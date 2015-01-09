<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de las Ordenes de Deposito para un integrado
 */
class MandatosModelOdrlist extends JModelItem {
    protected $dataModelo;

    function __construct(){

        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();

        parent::__construct();
    }

    public function getOrdenes($integradoId = null){
        $listado = getFromTimOne::getOrdenesRetiro($this->integradoId);

        return $listado;
    }
}
?>