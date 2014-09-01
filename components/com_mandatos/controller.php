<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();
$integradoId = getFromTimOne::getIntegradoId($currUser->id);

if($currUser->guest){
	$app->redirect('index.php/login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
}

if( is_null($integradoId) ){
	$app->redirect('index.php/component/integrado/?view=solicitud', JText::_('MSG_REDIRECT_INTEGRADO_PRINCIPAL'), 'Warning');
}

class MandatosController extends JControllerLegacy {
	function editarproyecto(){
		$app			= JFactory::getApplication();
		$input 			= JFactory::getApplication()->input;
		$data 			= $input->getArray();
		$userLog		= JFactory::getUser();
		$integradoId	= getFromTimOne::getIntegradoId($userLog->id);
		$proyectos 		= getFromTimOne::getProyects($integradoId['integrado_id']);
		
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
	
	function editarproducto(){
		$app			= JFactory::getApplication();
		$input	 		= JFactory::getApplication()->input;
		$data 			= $input->getArray();
		$userLog		= JFactory::getUser();
		
		$integrado_id	= getFromTimOne::getIntegradoId($userLog->id);
		
		$productos 		= getFromTimOne::getProducts($integrado_id['integrado_id']);
		if($userLog->guest){
			$app->redirect('index.php/login');
		}
		
		foreach ($productos as $key => $value) {
			if( $data['prodId'] == $value->id ){
				$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altaproductos&prodId='.$data['prodId']));
			}
		}
	}
	
	function editarclientes(){
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_mandatos&view=clientes'), 'Por el momento no es posible crear ni editar');
	}
}