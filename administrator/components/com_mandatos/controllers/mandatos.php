<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controlleradmin');

/**
 * 
 */
class MandatosControllerMandatos extends JControllerAdmin {

	public function getModel($name = 'Mandatos', $prefix = 'MandatosModel') 
	{
	        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	        return $model;
	}	

}

