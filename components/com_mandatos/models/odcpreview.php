<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelOdcpreview extends JModelItem {

    public $odc;

    public function __construct()
    {
        $this->inputVars 		 = JFactory::getApplication()->input->getArray();

        parent::__construct();
    }

    public function getOrdenes(){

        if (!isset($odcs)) {
            $odc = getFromTimOne::getOrdenesCompra($this->inputVars['integradoId'], $this->inputVars['idOrden']);
        }

        $this->odc = $odc[0];

        // Verifica si la ODC exite para el integrado o redirecciona
        if (is_null($this->odc)){
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODC_INVALID'), 'error');
        }

        $this->getDataFactura($this->odc);

        return $this->odc;
    }


    public function getIntegrado()	{
        return new IntegradoSimple($this->inputVars['integradoId']);
	    }

    public function getDataFactura($orden){
        getFromTimOne::getDataFactura($orden);
    }
}

