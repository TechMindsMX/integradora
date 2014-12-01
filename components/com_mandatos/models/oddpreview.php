<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelOddpreview extends JModelItem {

	public $odd;
	
	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();
		
		parent::__construct();
	}
	
	public function getOrdenes(){
		if (!isset($odds)) {
			$odd = getFromTimOne::getOrdenesDeposito($this->inputVars['integradoId'], $this->inputVars['idOrden']);
		}

		$this->odd = $odd[0];

		// Verifica si la ODD exite para el integrado o redirecciona
		if (is_null($this->odd)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->inputVars['integradoId']), JText::_('ODD_INVALID'), 'error');
		}
		
		return $this->odd;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}
	
}

