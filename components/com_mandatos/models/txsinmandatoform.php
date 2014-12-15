<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatoform extends JModelItem {

	protected $txs;
	protected $orders;

	public function getItem( $idTX ){
		$integradoId = $this->getIntegradoId();

		$this->txs = getFromTimOne::getTxIntegradoSinMandato($integradoId, $idTX);

		return $this->txs;
	}

	public function getOrders() {
		$integradoId = $this->getIntegradoId();

		$this->orders = getFromTimOne::getAllOrders($integradoId);

		return $this->orders;
	}

	private function getIntegradoId() {
		$data 				= JFactory::getApplication()->input->getArray();
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');
		$integradoId	    = isset($integradoId) ? $integradoId : $data['integradoId'];

		return $integradoId;
	}

}