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
class MandatosModelAltaproyectos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyectos(){
		$app			= JFactory::getApplication();
		$currUser		= JFactory::getUser();
		$data			= $app->input->getArray();
		$integradoId	= $data['integradoId'];
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		return 'hola';
	}
	
	public function getProyecto(){
		$app			= JFactory::getApplication();
		$currUser		= JFactory::getUser();
		$input 			= JFactory::getApplication()->input;
		$data			= $input->getArray();
		$integradoId	= $data['integradoId'];
		$integrado		= new Integrado;
		$isValid		= $integrado->isValid($integradoId, $currUser->id);
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}elseif(!$isValid){
			$app->redirect('index.php', 'necesita ser integrado principal');
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

