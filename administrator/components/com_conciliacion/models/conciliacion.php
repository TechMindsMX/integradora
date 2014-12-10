<?php

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );
jimport( 'integradora.validator' );
jimport( 'integradora.integrado' );
jimport( 'integradora.gettimone' );


/**
 * Methods supporting a list of Donde_comprar records.
 */
class ConciliacionModelConciliacion extends JModelList {

	public function getStpTxSinMandato() {

		$stp = getFromTimOne::getTxSinMandato();
		foreach ( $stp as $keys => $values ) {

			$integ = new IntegradoSimple($values->integradoId);
			$values->integradoName = $integ->getDisplayName();
			$data[] = $values;
		}

		return $data;
	}

}
