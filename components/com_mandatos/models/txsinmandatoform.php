<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatoform extends JModelItem {

	protected $txs;

	public function getItem( $idTX ){
		$data 				= JFactory::getApplication()->input->getArray();
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');
		$integradoId	    = isset($integradoId) ? $integradoId : $data['integradoId'];

		$this->txs = getFromTimOne::getTxIntegradoSinMandato($integradoId, $idTX);

		return $this->txs;
	}

}