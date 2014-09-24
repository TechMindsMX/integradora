<?php
defined('_JEXEC') or die('Restricted Access');

class MandatosModelOdvpreview extends JModelItem {

	public $odv;

	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();

		parent::__construct();
	}

	public function getOrdenes(){

		if (!isset($odvs)) {
			$odvs = getFromTimOne::getOrdenesVenta($this->inputVars['integradoId']);
		}

		foreach ($odvs as $key => $value) {
			if ($value->id == $this->inputVars['odvnum'] ) {
				$this->odv = $value;
			}
		}

		// Verifica si la ODV exite para el integrado o redirecciona
		if (is_null($this->odv)){
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_mandatos&integradoId='.$this->inputVars['integradoId']), JText::_('ODV_INVALID'), 'error');
		}

		return $this->odv;
	}

	public function getIntegrado()	{
		return new IntegradoSimple($this->inputVars['integradoId']);
	}

}