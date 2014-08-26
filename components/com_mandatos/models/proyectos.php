<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

/**
 * Modelo de datos para Listado de proyectos por usuario pincipal integrado
 */
class MandatosModelProyectos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyectos(){
		$app		= JFactory::getApplication();
		$currUser	= JFactory::getUser();
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		$this->dataModelo = getFromTimOne::getProyects($currUser->id);
		
		return $this->dataModelo;
	}
}

