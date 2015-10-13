<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 13-Oct-15
 * Time: 8:18 AM
 */

namespace Integralib;

use Cashout;
use getFromTimOne;
use PdfsIntegradora;
use sendToTimOne;
use transferFunds;

defined('_JEXEC') or die( 'Restricted access' );

class OdPrestamo
{
    public $id;
    public $integradoDeudor;
    public $integradoAcreedor;
    public $capital;
    public $deudorDataBank;
    public $orderType = 'odp';

    /**
     * OdPrestamo constructor.
     *
     * @param $orderId
     */
    public function __construct($orderId = null)
    {
        if (isset( $orderId )) {

            $order = getFromTimOne::getOrdenesPrestamo($orderId);

            foreach ($order[0] as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function pay()
    {
        $save = new sendToTimOne();

        if ( ! empty( $this->integradoDeudor->usuarios )) { //operacion de transfer entre integrados
            $txData = new transferFunds($this, $this->integradoAcreedor, $this->integradoDeudor, $this->capital);
            $txDone = $txData->sendCreateTx();
        } else {
            $txData = new Cashout($this, $this->integradoAcreedor, $this->integradoDeudor, $this->capital,
                array ('accountId' => $this->deudorDataBank->datosBan_id));
            $txDone = $txData->sendCreateTx();
        }

        if ($txDone) {
            $save->updateDB('ordenes_prestamo', array ('status = 13'), 'id = ' . $this->id);
        } else {
            $save->updateDB('ordenes_prestamo', array ('status = 1'), 'id = ' . $this->id);
        }

        return $txDone;
    }

    public function generate($idMutuo, $userId)
    {
        $timezone = new \DateTimeZone('America/Mexico_City');
        $mutuos   = getFromTimOne::getMutuos(null, $idMutuo);
        $mutuo    = $mutuos[0];

        if ($mutuo->status == 5) {
            $jsontabla = json_decode($mutuo->jsonTabla);
            $save      = new sendToTimOne();

            if (isset( $jsontabla->amortizacion_capital_fijo )) {
                $tabla = $jsontabla->amortizacion_capital_fijo;
            } else {
                $tabla = $jsontabla->amortizacion_cuota_fija;
            }

            $elemento0 = new \stdClass();

            $elemento0->periodo   = 0;
            $elemento0->inicial   = $mutuo->totalAmount;
            $elemento0->cuota     = $mutuo->totalAmount;
            $elemento0->intiva    = 0;
            $elemento0->intereses = 0;
            $elemento0->iva       = 0;
            $elemento0->acapital  = $mutuo->totalAmount;
            $elemento0->final     = 0;

            array_unshift($tabla, $elemento0);

            foreach ($tabla as $key => $objeto) {
                $odp   = new \stdClass();
                $fecha = new \DateTime('now', $timezone);

                $odp->idMutuo           = $idMutuo;
                $odp->numOrden          = $idMutuo . '-' . ( $key );
                $odp->fecha_elaboracion = $fecha->getTimestamp();
                $fechaDeposito          = $this->calcFechaDeposito($fecha, $mutuo->paymentPeriod, $key);
                $odp->fecha_deposito    = $fechaDeposito->getTimestamp();
                $odp->tasa              = $jsontabla->tasa_periodo;
                $odp->tipo_movimiento   = 'Integrado a Integrado';
                $odp->integradoIdA      = $mutuo->integradoIdE;
                $odp->acreedor          = $mutuo->integradoAcredor->nombre;
                $odp->a_rfc             = $mutuo->integradoAcredor->rfc;
                $odp->integradoIdD      = $mutuo->integradoIdR;
                $odp->deudor            = $mutuo->integradoDeudor->nombre;
                $odp->d_rfc             = $mutuo->integradoDeudor->rfc;
                $odp->capital           = $objeto->cuota;
                $odp->intereses         = $objeto->intereses;
                $odp->iva_intereses     = $objeto->iva;
                $odp->status            = 5;

                $save->formatData($odp);

                $saved = $save->insertDB('ordenes_prestamo', null, null, true);

                if ( ! $saved) {
                    //Si existe un error al generar la ODP se eliminan todas las odps creadas asi como las autorizaciones y se regresa al status 3
                    $save->deleteDB('ordenes_prestamo', 'idMutuo=' . $idMutuo);
                    $save->changeOrderStatus($idMutuo, 'mutuo', '3');
                    $save->deleteDB('auth_mutuo',
                        'idOrden = ' . $idMutuo . ' && userId = ' . $userId . ' && integradoId = ' . \JFactory::getSession()->get('integradoId',
                            null, 'integrado'));

                    $resultado = false;
                    break;
                } else {
                    $resultado        = true;
                    $createPdf        = new PdfsIntegradora();
                    $odp->id          = $saved;
                    $odp->createdDate = date('d-m-Y', $odp->fecha_elaboracion);
                    $createPdf->createPDF($odp, 'odp');
                }
            }
        } elseif ($mutuo->status == 3) {
            $resultado = false;
        }

        return $resultado;
    }
}