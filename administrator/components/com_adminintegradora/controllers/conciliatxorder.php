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
        $post = array('idTx' => 'INT', 'idOrden' => 'INT', 'orderType' => 'STRING');
        $input = JFactory::getApplication()->input->getArray($post);

        $tx = $this->getTx($input['idTx']);
        $order = $this->getOrder($input);

        $conciliacion = new ConciliaTxOrder($tx,$order);
        $conciliacion->saveRelations();

        JFactory::getApplication()->redirect('index.php?option=com_adminintegradora&view=oddlist');
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
                $order =getFromTimOne::getOrdenesDeposito(null, $post['idOrden']);
                break;
            case 'odv':
                $order =getFromTimOne::getOrdenesVenta(null, $post['idOrden']);
                break;
        }

        return $order[0];
    }
}