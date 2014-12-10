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
		$data = array();

		$stp = getFromTimOne::getTxSinMandato();

		if ( ! empty( $stp ) ) {
			foreach ( $stp as $keys => $values ) {
				$integ = new IntegradoSimple($values->idIntegrado);
				$values->integradoName = $integ->getDisplayName();
				$data[] = $values;
			}
		}

		return $data;
	}

}
