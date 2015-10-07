<?php
/**
 * Created by PhpStorm.
 * User: lutek-tim
 * Date: 18/03/2015
 * Time: 11:39 AM
 */

namespace Integralib;


class ConciliaTxOrder {
    private $tx;
    private $order;

    function __construct($objTx,$objOrden)
    {
        $this->tx = $objTx;
        $this->order = $objOrden;
    }

    public function saveRelations() {
        $objToInsert = new \stdClass();

        $objToInsert->id = $this->tx->id;
        $objToInsert->amount = $this->setAmountTxToAssign();
        $objToInsert->idOrden = $this->order->getId();
        $objToInsert->orderType = $this->order->getOrderType();

        $db = \JFactory::getDbo();
        $db->transactionStart();

        try {
            $db->insertObject( '#__txs_mandatos', $objToInsert );

            if ( ($this->order->balance - $objToInsert->amount) === 0.0 ) {
                $ststus = new \sendToTimOne;
                if (!$ststus->changeOrderStatus($this->order->getId(), $this->order->getOrderType(), 13) ) {
                    throw new \Exception('LBL_CHANGE_STATUS_FAILED');
                }
            }

            $db->transactionCommit();

            $result = true;

        }
        catch ( \Exception $e ) {
            $db->transactionRollback();

            $result = false;
        }

        return $result;
    }

    private function setAmountTxToAssign() {
        return $this->tx->balance > $this->order->balance ? $this->order->balance : $this->tx->balance;
    }
}