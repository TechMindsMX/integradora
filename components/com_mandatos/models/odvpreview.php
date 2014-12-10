<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdvpreview extends JModelItem {

	public $odv;

	public function __construct(){
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();

		parent::__construct();
	}

	public function getOrdenes(){

		if (!isset($odv)) {
			$odv = getFromTimOne::getOrdenesVenta($this->inputVars['integradoId'], $this->inputVars['idOrden']);
		}

		$this->getProyectFromId($this->odv->projectId);
		$this->getClientFromID($this->odv->clientId);

		// Verifica si la ODV exite para el integrado o redirecciona
		if (is_null($this->odv)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->inputVars['integradoId']), JText::_('ODV_INVALID'), 'error');
		}

		$this->odv->account = getFromTimOne::selectDB('integrado_datos_bancarios', 'datosBan_id = '.$this->odv->account);

		return $this->odv;
	}


	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}

}