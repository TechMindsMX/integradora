<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewMandatos extends JViewLegacy {

	protected $alta;

	function display($tpl = null){
		if ( $this->getLayout() == 'altas' ) {
			$this->alta 		= $this->get('alta');
		}

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		parent::display($tpl);
	}
}