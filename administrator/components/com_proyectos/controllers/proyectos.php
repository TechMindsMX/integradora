<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controlleradmin');

/**
 * 
 */
class ProyectosControllerProyectos extends JControllerAdmin {

	public function getModel($name = 'Proyectos', $prefix = 'ProyectosModel') 
	{
	        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	        return $model;
	}	

}

