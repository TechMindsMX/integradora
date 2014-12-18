<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatoform extends JModelItem {

	protected $txs;
	protected $orders;

	function __construct() {
		$this->vars = $this->getVars();

		parent::__construct();
	}

	public function getItem( $idTX ){
		$integradoId = $this->getIntegradoId();

		$this->txs = getFromTimOne::getTxIntegradoSinMandato($integradoId, $idTX);

		return $this->txs;
	}

	public function getOrders() {
		$integradoId = $this->getIntegradoId();

		$this->orders = getFromTimOne::getOrdersCxP($integradoId);

		if(isset($this->vars['numOrden']) && isset($this->vars['orderType']) && JSession::checkToken( 'get' )) {
			$this->orders = $this->getOrderByIdAndType($this->orders, $this->vars['numOrden'], $this->vars['orderType']);
		}

		return $this->orders;
	}

	public function getOrderByIdAndType($unpaidOrders, $orderId, $orderType ){
		foreach ( $unpaidOrders as $key => $orderArray ) {
			if ($key == $orderType) {
				foreach ( $orderArray as $orden ) {
					if($orden->id == $orderId) {
						$order = $orden;
					}
				}
			}
		}

		return $order;
	}

	private function getIntegradoId() {
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');
		$integradoId	    = isset($integradoId) ? $integradoId : $this->vars['integradoId'];

		return $integradoId;
	}

	private function getVars( ){
		return JFactory::getApplication()->input->getArray();
	}

}