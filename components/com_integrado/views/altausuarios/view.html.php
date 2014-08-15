<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewAltausuarios extends JViewLegacy {
	
	function display($tpl = null)
	{
		$this->data = $this->get('Usuarios');
		
		$this->catalogos = $this->get('catalogos');
		
		$usuario = JFactory::getUser();
		$modelo  = $this->getModel();
		
		$canDo = Autoriza::__($usuario);
		var_dump($canDo, $usuario, $modelo);
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}
}