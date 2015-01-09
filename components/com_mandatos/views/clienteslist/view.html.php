<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewClienteslist extends JViewLegacy {
	public $data;
	public $catalogoBancos;
	protected $integradoId;

	function display($tpl = null){
		$data 				= JFactory::getApplication()->input->getArray();

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$model = $this->getModel();

		$this->data = $model->getClientes($this->integradoId);

		$this->catalogoBancos = $model->getCatalogos();

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}
}