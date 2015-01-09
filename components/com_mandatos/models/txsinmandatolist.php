<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatolist extends JModelItem {

	public function getItems( ){
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');

		$txs = getFromTimOne::getTxIntegradoSinMandato($integradoId);

		return $txs;
	}

}