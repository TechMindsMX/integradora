<?php

defined('_JEXEC') or die('Restricted Access');
use Integralib\ConciliaTxOrder;
use Integralib\Txs;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.gettimone');
jimport('integradora.notifications');
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
        $this->order = $order;
        $this->sendEmail();
        JFactory::getApplication()->redirect('index.php?option=com_adminintegradora&view='.$input['orderType'].'list');
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

    public function sendEmail(){
        /*
         *  NOTIFICACIONES 35
         */

        if($this->order->paymentMethod->id==1){
            $metodoPago = JText::_('LBL_SPEI');
        }
        if($this->order->paymentMethod->id==2) {
            $metodoPago = JText::_('LBL_DEPOSIT');
        }
        if($this->order->paymentMethod->id==3) {
            $metodoPago = JText::_('LBL_CHEQUE');
        }


        $getCurrUser     = new IntegradoSimple($this->integradoId);
        $titleArray      = array( $this->order->numOrden);
        $name = $this->order->receptor->getDisplayName();

        $array           = array(
            $name,
            $this->order->numOrden,
            date('d-m-Y'),
            '$'.number_format($this->order->totalAmount, 2),
            $metodoPago
        );

        $send            = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]            = $send->sendNotifications('35', $array, $titleArray);

        /*
         * Notificaciones 9
         */

        $titleArrayAdmin = array( $name, $this->order->numOrden );
        $arrayAdmin      = $array;

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('36', $arrayAdmin, $titleArrayAdmin);

        return $info;
    }
}