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
class MandatosModelProyectoslist extends JModelItem {
	
	protected $dataModelo;
	
	public function getProyectos(){
		$joomlaId		= JFactory::getUser()->id;
		$data			= JFactory::getApplication()->input->getArray();
		$integradoId	= $data['integradoId'];
		
		$this->dataModelo = getFromTimOne::getProyects($integradoId['integrado_id']);

		return $this->dataModelo;
	}
}

