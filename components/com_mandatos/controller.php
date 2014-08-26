<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();
if($currUser->guest){
	$app->redirect('index.php/login');
}

class MandatosController extends JControllerLegacy {
	function editarproyecto(){
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
					$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altaproyectos&proyId='.$data['proyId']));
				}else{
					$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altasubproyectos&proyId='.$data['proyId']));
				}
			}
		}
		exit;
	}	
}