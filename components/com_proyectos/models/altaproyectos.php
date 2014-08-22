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
class ProyectosModelAltaproyectos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyectos(){
		$app		= JFactory::getApplication();
		$currUser	= JFactory::getUser();
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		return 'hola';
	}
	
	public function getProyecto(){
		$app		= JFactory::getApplication();
		$currUser	= JFactory::getUser();
		$input 		= JFactory::getApplication()->input;
		$data		= $input->getArray();
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		$allproyects = getFromTimOne::getProyects($currUser->id);

		foreach ($allproyects as $key => $value) {
			if($value->id == $data['proyId']){
				$this->proyecto = $value; 
			}
		}

		return $this->proyecto;
	}
}

