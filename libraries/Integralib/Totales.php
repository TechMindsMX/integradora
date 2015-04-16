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
    }

    private function calculateTotalimpuestosTrasladados($objImpuestos){
        $totalImpuestosTrasladados = 0;
        foreach($objImpuestos as $value){
            $totalImpuestosTrasladados += $value->importe;
        }

        return $totalImpuestosTrasladados;
    }

}