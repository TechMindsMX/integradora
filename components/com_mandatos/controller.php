<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

$app 		 = JFactory::getApplication();
$currUser	 = JFactory::getUser();
$data 		 = JFactory::getApplication()->input->getArray();
$integradoId = $data['integradoId'];
$integrado	 = new Integrado;
$isValid 	 = $integrado->isValid($integradoId, $currUser->id);

if($currUser->guest){
	$app->redirect('index.php/login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
}

if( is_null($integradoId) || !$isValid){
	$app->redirect('index.php/component/integrado/?view=solicitud', JText::_('MSG_REDIRECT_INTEGRADO_PRINCIPAL'), 'Warning');
}

class MandatosController extends JControllerLegacy {
	function editarproyecto(){
		$app			= JFactory::getApplication();
		$input 			= $app->input;
		$data 			= $input->getArray();
		$userLog		= JFactory::getUser();
		$integradoId	= $data['integradoId'];
		$proyectos 		= getFromTimOne::getProyects($integradoId);
		
		if($userLog->guest){
			$app->redirect('index.php/login');
		}

		foreach ($proyectos as $key => $value) {
			if($data['proyId'] == $value->id){
				if($value->parentId == 0){
					$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altaproyectos&proyId='.$data['proyId'].'&integradoId='.$integradoId));
				}else{
					$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altasubproyectos&proyId='.$data['proyId'].'&integradoId='.$integradoId));
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
		$integrado_id	= $data['integradoId'];
		$productos 		= getFromTimOne::getProducts($integrado_id);

		if($userLog->guest){
			$app->redirect('index.php/login');
		}
		
		foreach ($productos as $key => $value) {
			if( $data['prodId'] == $value->id ){
				$app->redirect(JRoute::_('index.php?option=com_mandatos&view=altaproductos&prodId='.$data['prodId'].'&integradoId='.$data['integradoId']));
			}
		}
	}
	
	function editarclientes(){
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_mandatos&view=clientes'), 'Por el momento no es posible crear ni editar');
	}

	function simulaenvio(){
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_mandatos'), 'Datos recibidos');
	}
	
	function searchrfc(){
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$input 	= $app->input;
		$data 	= $input->getArray();
		$where	= $db->quoteName('rfc').' = '.$db->quote($data['rfc']);
		$respuesta = '';
		
		$rfcPersonas = validador::valida_rfc($data, 'rfc', 'dp_');
		$rfcEmpresa	= validador::valida_rfc($data, 'rfc', 'de_');
		
		if($rfcEmpresa){
			$tipo_rfc = 1;
		}elseif($rfcPersonas){
			$tipo_rfc = 2;
		}else{
			$respuesta['success'] = false;
			$respuesta['msg'] = JText::_('MSG_RFC_INVALID');
			
			echo json_encode($respuesta);
			exit;
		}
		
		$existe = querysDB::checkData('integrado_datos_personales', $where);
		if(empty($existe)){
			$existe = querysDB::checkData('integrado_datos_empresa', $where);
		}
		
		if(!empty($existe)){
			$datos = new Integrado;
			$datos->integrados[0]->success = true;
			echo json_encode($datos->integrados[0]);
		}else{
			$respuesta['success'] = false;
			$respuesta['msg'] = JText::_('MSG_RFC_NO_EXIST');
			$respuesta['pj_pers_juridica'] = $tipo_rfc;
			
			echo json_encode($respuesta);
		}
	}

	function agregarBanco(){
		$app 		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$input		= $app->input;
		$data		= $input->getArray();
		$validacion = validador::valida_banco_clabe($data, 'db_banco_clabe');
		
		$respuesta['banco'] 	= $data['db_banco_nombre'];
  		$respuesta['cuenta'] 	= $data['db_banco_cuenta'];
  		$respuesta['sucursal'] 	= $data['db_banco_sucursal'];
  		$respuesta['clabe']	 	= $data['db_banco_clabe'];
		$respuesta['idCuenta']	= ($data['db_banco_sucursal']*1)+1;
		
		echo json_encode($respuesta);
	}
}