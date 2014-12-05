<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewTxsinmandatolist extends JViewLegacy {

	protected $txs;

	function display($tpl = null){
		$this->txs 		    = $this->get('items');

		$data 				= JFactory::getApplication()->input->getArray();
		$sesion             = JFactory::getSession();
		$this->integradoId  = $sesion->get('integradoId', null, 'integrado');
		$this->integradoId	= isset($integradoId) ? $integradoId : $data['integradoId'];

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }

		parent::display($tpl);
	}
}