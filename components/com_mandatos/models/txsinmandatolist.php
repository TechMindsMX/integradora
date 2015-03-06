<?php
use Integralib\Txs;

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatolist extends JModelItem {

	public function getItems( ){
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');

		$txs = getFromTimOne::getTxIntegradoSinMandato($integradoId);

		foreach ( $txs as $trans ) {
			$trans->balance = $this->getTxBalance($trans);
		}

		return $txs;
	}

	/**
	 * @param $trans
	 * se traen los mandatos a los que esta asosciada la Tx
	 * @return mixed
	 */
	private function getTxBalance( $trans ) {
		$txs = new Txs();

		return $txs->calculateBalance($trans);
	}


}