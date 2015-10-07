<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewTxsinmandatolist extends JViewLegacy {

	protected $txs;
	protected $integradoId;

	function display($tpl = null){
		$this->loadHelper('Mandatos');
		$this->txs 		    = MandatosHelper::getTXsinMandato();

		$sesion             = JFactory::getSession();
		$this->integradoId  = $sesion->get('integradoId', null, 'integrado');

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }

		parent::display($tpl);
	}
}