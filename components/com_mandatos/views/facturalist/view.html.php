<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewfacturalist extends JViewLegacy {
	protected $integradoId;
	protected $data;

	function display($tpl = null){

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$model = $this->getModel();
		$this->data  = $model->getFacturas( $this->integradoId );

		if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }



		parent::display($tpl);
	}
}