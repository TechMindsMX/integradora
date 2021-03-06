<?php

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );
jimport( 'integradora.validator' );
jimport( 'integradora.integrado' );
jimport( 'integradora.gettimone' );


/**
 * Methods supporting a list of Donde_comprar records.
 */
class ConciliacionbancoModelConciliacionbanco extends JModelList {

	public function getUserIntegrado() {

		$factura    = new Integrado();
		$integrados = $factura->getIntegrados();

		return $integrados;
	}

	public function getSTP() {

		$stp = getFromTimOne::getTxSinMandato();
		foreach ( $stp as $keys => $values ) {

			$data[] = $values;

		}

		return $data;
	}

}
