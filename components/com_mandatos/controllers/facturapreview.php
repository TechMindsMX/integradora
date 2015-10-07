<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.notifications');
/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 */
class MandatosControllerFacturapreview extends JControllerAdmin {

    protected $integradoId;

    function cancel() {
        $this->app 			= JFactory::getApplication();

        $idODV = $this->input->get('facturanum', null, 'INT');

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );
        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canAuth'] && !is_null($idODV) ) {
            // acciones cuando tiene permisos para autorizar
            $db = JFactory::getDbo();
            try {
                $db->transactionStart();
                $save = new sendToTimOne();
                $changeStatus = $save->changeOrderStatus($idODV, 'odv', $this->getStatusId('Cancelada'));

                if ($changeStatus) {
                    $orden = new \Integralib\OdVenta();
                    $orden->setOrderFromId($idODV);

                    $userRfc = $orden->getEmisor()->getIntegradoRfc();
                    $xmlUUID = $orden->getfacturaUUID();

                    if(!$xmlUUID){
                        throw new Exception(JText::_('LBL_FACT_CANCELED_NOT_POSSIBLE'));
                    }else {
                        $request = new \Integralib\TimOneRequest();

                        $canceled = $request->sendCancelFactura($userRfc, $xmlUUID);

                        if ($canceled) {
                            $this->app->enqueueMessage(JText::_('LBL_FACT_CANCELED_SUCCESSFULY'));
                            $this->sendEmail();
                        } else {
                            throw new Exception(JText::_('LBL_FACT_CANCELED_FAILED'));
                        }
                    }
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $db->transactionRollback();
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->app->redirect('index.php?option=com_mandatos&view=facturapreview&facturanum='.$idODV);
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->enqueueMessage(JText::_('LBL_CANT_AUTHORIZE'), 'error');
        }
        $this->app->redirect('index.php?option=com_mandatos&view=facturalist');
    }

    /**
     * @param $dato
     */
    private function sendEmail()
    {
        /*
         * NOTIFICACIONES 23
         */
        $getCurrUser 		= new IntegradoSimple($this->integradoId);
        $info 				= array();
        $facturaNum 		= $this->app->input->getArray();
        $facturas 			= getFromTimOne::getFacturasVenta($this->integradoId);

        foreach ($facturas as $key => $value) {

            if($value->id == $facturaNum['facturanum']){
                $dataFactura = $value;
            }
        }
        $arrayTitle 		= array($dataFactura->numOrden);
        $array				= array($getCurrUser->getUserPrincipal()->name, $dataFactura->numOrden, JFactory::getUser()->username, date('d-m-Y'), $dataFactura->totalAmount, $dataFactura->proveedor->tradeName, $dataFactura->createdDate, $dataFactura->numOrden);

        $send = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);

        $info[] = $send->sendNotifications('23', $array, $arrayTitle);

        /*
         * Notificaciones 24
         */

        $arrayTitleAdmin 	= array($getCurrUser->getUserPrincipal()->name, $dataFactura->numOrden);

        $send = new Send_email();
        $send->setAdminEmails();
        $info[] 			= $send->sendNotifications('24', $array, $arrayTitleAdmin);
    }

    private function getStatusId( $string ) {
        $statusCat = getFromTimOne::getOrderStatusCatalogByName();

        return $statusCat[$string]->id;
    }
}
