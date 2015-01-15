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
class MandatosModelSubproyectosform extends JModelItem {
	
	protected $dataModelo;
	protected $integradoId;

	public function getProyectos(){
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$allProjects 	= getFromTimOne::getProyects($this->integradoId);
		
		foreach ($allProjects as $key => $value) {
			if( $value->parentId == 0){
				$this->dataModelo[] = $value;
			}
		}
		
		return $this->dataModelo;
	}
	
	public function getProyecto(){
		$currUser	= JFactory::getUser();
        $app       		= JFactory::getApplication();
        $post           = array('id_proyecto'=>'INT');
        $data			= $app->input->getArray($post);

        if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		$proyecto = getFromTimOne::getProyects(null, $data['id_proyecto']);

        $this->proyecto = $proyecto[$data['id_proyecto']];

		return $this->proyecto;
	}
}
