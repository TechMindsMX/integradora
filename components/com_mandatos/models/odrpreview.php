<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdrpreview extends JModelItem {

	public $odr;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();

		parent::__construct();
	}

	public function getOrdenes(){

		$odr = getFromTimOne::getOrdenesRetiro($this->inputVars['integradoId'], $this->inputVars['idOrden']);
		$this->odr = $odr[0];

		// Verifica si la ODR exite para el integrado o redirecciona
		if (is_null($this->odr)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->inputVars['integradoId']), JText::_('ODR_INVALID'), 'error');
		}

		// simulado
		$this->odr->currency = 'MXN';

		return $this->odr;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}

}