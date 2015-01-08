<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de las Ordenes de Deposito para un integrado
 */
class MandatosModelOddlist extends JModelItem {
	protected $dataModelo;

    function __construct(){
        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integrado 	= new Integrado;
        $this->currUser	    = Jfactory::getUser();

        parent::__construct();
    }
	
	public function getOrdenes(){
        $respuesta = getFromTimOne::getOrdenesDeposito($this->integradoId);

        return $respuesta;
    }
}
?>