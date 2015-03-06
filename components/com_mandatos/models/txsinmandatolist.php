<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');


class MandatosModelTxsinmandatolist extends JModelItem {

	public function getItems( ){
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');

		$txs = getFromTimOne::getTxIntegradoSinMandato($integradoId);

		foreach ( $txs as $trans ) {
			$trans->orders = $this->getMandatosOfTx($trans);
			$this->checkIfTxIsDepleted($trans);
		}


		return $txs;
	}

	/**
	 * @param $trans
	 * se traen los mandatos a los que esta asosciada la Tx
	 * @return mixed
	 */
	private function getMandatosOfTx( $trans ) {
		$db = JFactory::getDbo();
		$subquery = $db->getQuery(true);
		$query = $db->getQuery(true);
		$query->select( 'b.amount, '.$db->quoteName('t.id', 'id_txs_timone').', t.idTx, t.idOrden, t.tipoOrden, t.idComision')
		      ->from($db->quoteName('#__txs_banco_integrado', 'b'))
		      ->join('INNER', $db->quoteName('#__txs_timone_mandato', 't'). ' ON (t.id IN ('.
		                      $subquery->select('p.id_txs_timone')
		                               ->from( $db->quoteName('#__txs_banco_timone_relation', 'p' ))
		                               ->where('p.id_txs_banco = b.id')
		                      .'))')
		      ->where('b.id = '. $db->quote($trans->id) );
		$r = $db->setQuery($query);
		$txs = $r->loadObjectList();

		return $txs;
	}

	private function checkIfTxIsDepleted( $trans ) {

	}

}