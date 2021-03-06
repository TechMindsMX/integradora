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
	public $folio;

    function __construct(OdVenta $objOdv, $objImpuestos, $series)
    {
        $this->total = $objOdv->getTotalAmount();
        $this->subtotal = $objOdv->subTotalAmount;

        $this->totalImpuestosTrasladados = $this->calculateTotalimpuestosTrasladados($objImpuestos);

	    $this->folio = $this->setFolio($series);

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
	private function setFolio( $series ) {
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		            ->select('*')
		            ->from('#__facturas_folios');
		$db->setQuery($query);

		$result = $db->loadObject();

		return $this->getNextFolio($result, $series);
	}

	private function getNextFolio( $result, $series ) {
		$series = $this->getFolioSeries($series);
		$folio = $series.'1';

		if ( ! empty( $result ) ) {
			$folio = $series. ((int)str_replace($series, '', $result->folio) + 1);
		}

		return $folio;
	}

	private function getFolioSeries($series) {
		$validSeries = \Catalogos::getValidFacturaFolioSeries();

		if (!in_array($series, $validSeries)) {
			throw new \Exception( \JText::_('ERR_417_FOLIO') );
		}

		return $series;
	}

}