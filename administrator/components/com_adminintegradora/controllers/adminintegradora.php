<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controlleradmin');

/**
 * 
 */
class  AdminintegradoraControllerAdminintegradora extends JControllerAdmin {

	public function getModel($name = 'Adminintegradora', $prefix = 'ComisionModel')
	{
	        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	        return $model;
	}

	public function editar(){
		echo 'aqui manda a edicion';
	}

}

