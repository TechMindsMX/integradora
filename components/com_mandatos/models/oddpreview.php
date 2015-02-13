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
	private $idOrden;
	private $integradoId;

	public function __construct()
	{
		$this->idOrden 		 = JFactory::getApplication()->input->get('idOrden', null, 'INT');

		$session = JFactory::getSession();
		$this->integradoId 	= $session->get('integradoId', null, 'integrado');

		parent::__construct();
	}
	
	public function getOrdenes(){
		if (!isset($odds)) {
			$odd = getFromTimOne::getOrdenesDeposito($this->integradoId, $this->idOrden);
		}

		$this->odd = $odd[0];

		// Verifica si la ODD exite para el integrado o redirecciona
		if (is_null($this->odd)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODD_INVALID'), 'error');
		}
		
		return $this->odd;
	}

	public function getIntegrado()	{
		$integrado = new IntegradoSimple($this->integradoId);

        $integrado->getTimOneData();

        return $integrado;
	}
	
}

