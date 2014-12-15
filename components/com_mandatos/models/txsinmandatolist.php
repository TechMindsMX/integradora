<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatolist extends JModelItem {

	public function getItems( ){
		$data 				= JFactory::getApplication()->input->getArray();
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');

		$integradoId	    = isset($integradoId) ? $integradoId : $data['integradoId'];

		$txs = getFromTimOne::getTxIntegradoSinMandato($integradoId);

		return $txs;
	}

}