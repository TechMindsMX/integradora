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
class MandatosModelProyectosform extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyecto(){
		$app			= JFactory::getApplication();
		$currUser		= JFactory::getUser();
        $post           = array('integradoId'=>'INT','id_proyecto'=>'INT');
        $data   		= $app->input->getArray($post);
		$integrado		= new Integrado;
		$isValid		= $integrado->isValidPrincipal($data['integradoId'], $currUser->id);
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}elseif(!$isValid){
			$app->redirect('index.php', 'necesita ser integrado principal');
		}
		
		$dataProject = getFromTimOne::getProyects(null,$data['id_proyecto']);

		return $dataProject[0];
	}
}

