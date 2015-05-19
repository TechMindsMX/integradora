<?php
use Integralib\OrderFactory;

defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdvpreview extends JModelItem {

	protected $odv;

	public function __construct(){
		$this->inputVars 		 = JFactory::getApplication()->input->getArray(array('idOrden'=>'INT'));

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		parent::__construct();
	}

	public function getOrdenes(){

		if (!isset($odv)) {
			$odv = OrderFactory::getOrder($this->inputVars['idOrden'], 'odv');

			$this->odv = $odv;
		}

		// Verifica si la ODV exite para el integrado o redirecciona
		if (is_null($this->odv)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos'), JText::_('ODV_INVALID'), 'error');
		}

		$dbq = JFactory::getDbo();
		$this->odv->account = getFromTimOne::selectDB('integrado_datos_bancarios', 'datosBan_id = '. $dbq->quote($this->odv->account) );

		return $this->odv;
	}


	public function getIntegrado()	{
		return new IntegradoSimple($this->integradoId);
	}

}