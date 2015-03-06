<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.component.controller');

/**
 * 
 */
class MandatosController extends JControllerLegacy {
	
	function display($cacheable = false, $urlparams = false) {
		JFactory::getSession()->set('integradoId', 1, 'integrado');

		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'Mandatos'));
		
		parent::display($cacheable);
		
	}
}
