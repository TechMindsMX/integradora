<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controlleradmin');

/**
 * 
 */
class IntegardoControllerIntegrados extends JControllerAdmin {

	public function getModel($name = 'Integrado', $prefix = 'IntegradoModel') 
	{
	        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	        return $model;
	}	

}

