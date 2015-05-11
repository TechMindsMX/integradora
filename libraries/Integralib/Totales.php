<?php
/**
 * Created by PhpStorm.
 * User: lutek-tim
 * Date: 31/03/2015
 * Time: 10:32 AM
 */

namespace Integralib;


class Totales {
    public $total;
    public $subtotal;
    public $totalImpuestosTrasladados;

    function __construct(OdVenta $objOdv, $objImpuestos)
    {
        $this->total = $objOdv->getTotalAmount();
        $this->subtotal = $objOdv->subTotalAmount;

        $this->totalImpuestosTrasladados = $this->calculateTotalimpuestosTrasladados($objImpuestos);

	    $this->folio = $this->setFolio();

    }

    private function calculateTotalimpuestosTrasladados($objImpuestos){
        $totalImpuestosTrasladados = 0;
        foreach($objImpuestos as $value){
            $totalImpuestosTrasladados += $value->importe;
        }

        return $totalImpuestosTrasladados;
    }

	/**
	 * Sets folio using getFolioSeries
	 */
	private function setFolio() {
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		            ->select('*')
		            ->from('#__facturas_folios');
		$db->setQuery($query);

		$result = $db->loadObject();

		return $this->getNextFolio($result);
	}

	private function getNextFolio( $result ) {
		$series = $this->getFolioSeries();
		$folio = $series.'1';

		if ( ! empty( $result ) ) {
			$folio = $series. ((int)str_replace($series, '', $result->folio) + 1);
		}

		return $folio;
	}

	private function getFolioSeries() {
		return 'B';
	}

}