<?php

defined('_JEXEC') or die('Restricted Access');
use Integralib\ConciliaTxOrder;
use Integralib\Txs;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.gettimone');
/**
*
*/
class AdminintegradoraControllerConciliaTxOrder extends JControllerAdmin {
    public function save(){
        $post = array('idTx' => 10, 'idOrder' =>3, 'orderType' => 'odd');

        $tx = $this->getTx($post['idTx']);
        $order = $this->getOrder($post);

        $conciliacion = new ConciliaTxOrder($tx,$order);
        $conciliacion->saveRelations();
exit('hola');
    }

    /**
     * @param $idTX
     * @return mixed
     */
    public function getTx($idTX)
    {
        $tx = getFromTimOne::getTxIntegradoSinMandato(null, $idTX);

        foreach ($tx as $trans) {
            $trans->balance = $this->getTxBalance($trans);
        }
        return $tx[0];
    }

    private function getTxBalance( $trans ) {
        $txs = new Txs();

        return $txs->calculateBalance($trans);
    }

    private function getOrder($post){
        switch($post['orderType']){
            case 'odd':
                $order =getFromTimOne::getOrdenesDeposito(null, $post['idOrder']);
                break;
            case 'odv':
                $order =getFromTimOne::getOrdenesVenta(null, $post['idOrder']);
                break;
        }

        return $order[0];
    }
}