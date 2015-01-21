<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewProductoslist extends JViewLegacy {

	function display($tpl = null){

        $this->data         = $this->get('productos');
        $this->catalogo         = $this->get('CatalogoIva');

        // Check for errors.
        if (count($errors = $this->get('Errors')) ) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }
		
		parent::display($tpl);
	}
}