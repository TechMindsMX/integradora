<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.component.controller');

/**
 * 
 */
class ProyectosController extends JControllerLegacy {
	
	function display($cacheable = false, $urlparams = false) {
		
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'Proyectos'));
		
		parent::display($cacheable);
		
	}
}
