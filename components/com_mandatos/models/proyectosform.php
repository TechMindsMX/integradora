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
	protected $integradoId;

	public function getProyecto(){
		$app			= JFactory::getApplication();
		$currUser		= JFactory::getUser();
        $post           = array('id_proyecto'=>'INT');
        $data   		= $app->input->getArray($post);

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$integrado		= new Integrado;
		$isValid		= $integrado->isValidPrincipal($this->integradoId, $currUser->id);
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}elseif(!$isValid){
			$app->redirect('index.php', 'necesita ser integrado principal');
		}
		
		$dataProject = getFromTimOne::getProyects(null,$data['id_proyecto']);
        $dataProject = $dataProject[$data['id_proyecto']];


        if($this->integradoId != $dataProject->integradoId){
            $app->redirect(JRoute::_('index.php?option=com_mandatos&view=proyectoslist'));
        }

        return $dataProject;
	}

	public function getCatalogos() {
		return getFromTimOne::getBasicStatusCatalog();
	}
}

