<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdrpreview extends JModelItem {

	public $odr;

	public function __construct()
	{
		$this->inputVars 	= JFactory::getApplication()->input->getArray( array('idOrden' => 'INT') );
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		parent::__construct();
	}

	public function getOrdenes(){

		$odr = getFromTimOne::getOrdenesRetiro($this->integradoId, $this->inputVars['idOrden']);
		$this->odr = new \Integralib\OdRetiro(null, $odr[0]->id);

		// Verifica si la ODR exite para el integrado o redirecciona
		if (is_null($this->odr)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODR_INVALID'), 'error');
		}

		// simulado
		$this->odr->currency = 'MXN';

		return $this->odr;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->integradoId);
	}

}