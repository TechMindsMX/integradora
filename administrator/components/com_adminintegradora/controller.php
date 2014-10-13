<?php
defined('_JEXEC') or die('Restricted Access');

jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.rutas');

/**
 * 
 */
class AdminintegradoraController extends JControllerLegacy {
	
	function display($cacheable = false, $urlparams = false) {
		
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'Adminintegradora'));
		
		parent::display($cacheable);
		
	}



}
