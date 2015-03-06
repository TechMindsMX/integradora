<?php
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

class FacturasporcobrarControllerFactdata extends JControllerAdmin{

    function __construct(){

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $post = array('id' => 'INT');
        $this->app = JFactory::getApplication();

        $this->parametros = $this->app->input->getArray($post);
        $this->comisiones = getFromTimOne::getComisionesOfIntegrado($this->integradoId);

        parent::__construct();
    }

    function updatefact(){
        $return = false;
        $newStatusId = 13;
        $catalogoStatus = getFromTimOne::getOrderStatusCatalog();

        $save = new sendToTimOne();

        $TxOdc = $this->txComision();

        if($TxOdc){

            $statusChange = $save->changeOrderStatus($this->parametros['id'], 'odv', $newStatusId);
            if($statusChange) {
                    $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));
                    $return = 'done';
                }
            }else{
                $this->app->enqueueMessage(JText::sprintf('ORDER_PAID_AUTHORIZED', $catalogoStatus[$newStatusId]->name));
            }
        echo $return;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->getOrden();
        $montoComision  = getFromTimOne::calculaComision($orden, 'FACTURA', $this->comisiones);

        $orden->orderType = 'FACTURA';

        $txComision     = new transferFunds($orden,$orden->integradoId,1,$montoComision);

        return $txComision->sendCreateTx();
    }

    private function getOrden(){
        $orden = getFromTimOne::getOrdenesVenta(null, $this->parametros['id']);
        $orden = $orden[0];
        return $orden;
    }


}