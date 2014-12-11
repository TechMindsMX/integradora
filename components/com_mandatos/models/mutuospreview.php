<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para Listado de los clientes dados de alta para un integrado
 */
class MandatosModelMutuospreview extends JModelItem {


	public function __construct()
	{
		$this->inputVars 		 = JFactory::getApplication()->input->getArray();
		
		parent::__construct();
	}
	
}

