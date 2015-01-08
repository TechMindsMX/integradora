<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class MandatosViewOddlist extends JViewLegacy {
	public $integradoId;
	public $data;
	public $permisos;

	function display($tpl = null){

		$session = JFactory::getSession();
		$this->integradoId 	= $session->get('integradoId', null, 'integrado');

		$this->data         = $this->get('ordenes');

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}