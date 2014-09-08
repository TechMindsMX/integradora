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
class MandatosModelAltasubproyectos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyectos(){
		$joomlaId		= JFactory::getUser()->id;
		$data			= JFactory::getApplication()->input->getArray();
		$integradoId	= $data['integradoId'];
		$allProjects 	= getFromTimOne::getProyects($integradoId);
		
		foreach ($allProjects as $key => $value) {
			if( $value->parentId == 0){
				$this->dataModelo[] = $value;
			}
		}
		
		return $this->dataModelo;
	}
	
	public function getProyecto(){
		$app		= JFactory::getApplication();
		$currUser	= JFactory::getUser();
		$input 		= JFactory::getApplication()->input;
		$data		= $input->getArray();
		$integradoId	= $data['integradoId'];
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		$allproyects = getFromTimOne::getProyects($integradoId);

		foreach ($allproyects as $key => $value) {
			if($value->id == $data['proyId']){
				$this->proyecto = $value; 
			}
		}

		return $this->proyecto;
	}
}
?>