<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatoform extends JModelItem {

	protected $txs;
	protected $orders;
	protected $integradoId;

	function __construct() {
		$this->vars = JFactory::getApplication()->input->getArray();

		$sesion             = JFactory::getSession();
		$this->integradoId  = $sesion->get('integradoId', null, 'integrado');

		parent::__construct();
	}

	public function getItem( $idTX ){
		$this->txs = getFromTimOne::getTxIntegradoSinMandato($this->integradoId, $idTX);

		return $this->txs;
	}

	public function getOrders() {
		$this->orders = getFromTimOne::getOrdersCxP($this->integradoId);

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

}