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
	protected $integradoId;

	public function getProyectos(){
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$this->dataModelo = getFromTimOne::getProyects($this->integradoId);

		return $this->dataModelo;
	}
}

