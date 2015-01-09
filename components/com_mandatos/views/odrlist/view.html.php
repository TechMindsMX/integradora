<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdrlist extends JViewLegacy {
	protected $integradoId;
	protected $permisos;

	function display($tpl = null){

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$model = $this->getModel();
		$this->data         = $model->getOrdenes( $this->integradoId );

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		
		$this->loadHelper('Mandatos');

		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}