<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');

class ProyectosController extends JControllerLegacy {
	function editar(){
		$app		= JFactory::getApplication();
		$input 		= JFactory::getApplication()->input;
		$data 		= $input->getArray();
		$userLog	= JFactory::getUser();
		
		$proyectos 	= getFromTimOne::getProyects();
		
		if($userLog->guest){
			$app->redirect('index.php/login');
		}

		foreach ($proyectos as $key => $value) {
			if($data['proyId'] == $value->id){
				if($value->parentId == 0){
					$app->redirect('index.php/component/proyectos/?view=altaproyectos&proyId='.$data['proyId']);
				}else{
					$app->redirect('index.php/component/proyectos/?view=altasubproyectos&proyId='.$data['proyId']);
				}
			}
		}
		
		exit;
	}
	
}