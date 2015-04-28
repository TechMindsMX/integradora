<?php
use Integralib\OdVenta;
use Integralib\OrdenFn;
use Integralib\OrderFactory;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.notifications');

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 */
class MandatosControllerOdvpreview extends JControllerLegacy {

    function authorize() {
        $post               = array('idOrden' => 'INT');
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        $redirectUrl = 'index.php?option=com_mandatos&view=odvlist';

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']         = (INT)$user->id;
            $this->parametros['integradoId']    = $this->integradoId;
            $this->parametros['authDate']       = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odv_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $db = JFactory::getDbo();
            try {
                $db->transactionStart();

                $resultado = $save->insertDB( 'auth_odv' );

                if ( $resultado ) {
                    // autorización guardada
                    $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                    $newStatusId    = 5;
                    $statusChange   = $save->changeOrderStatus( $this->parametros['idOrden'], 'odv', $newStatusId );
                    if ( $statusChange ) {
                        $this->app->enqueueMessage( JText::sprintf( 'ORDER_STATUS_CHANGED',
                            $catalogoStatus[ $newStatusId ]->name ) );

                        $newOrder = OrderFactory::getOrder( $this->parametros['idOrden'], 'odv' );

                        if ( $newOrder->getStatus()->id == 5 && is_null( $newOrder->urlXML ) ) {
                            $factObj = $save->generaObjetoFactura( $newOrder );

                            if ( $factObj != false ) {
                                $xmlFactura = $save->generateFacturaFromTimone( $factObj );
                                try {
                                    $newOrder->urlXML = $save->saveXMLFile( $xmlFactura );
                                    $newOrder->XML    = $xmlFactura;
                                    $info             = $this->sendEmail( $newOrder );
                                }
                                catch ( Exception $e ) {
                                    $msg = $e->getMessage();
                                    JLog::add( $msg, JLog::ERROR, 'error' );
                                    $this->app->enqueueMessage( $msg, 'error' );
                                }
                            }

                            if ( isset( $newOrder->urlXML ) ) {
                                if ( $newOrder->urlXML != false ) {
                                    $save->formatData( array ( 'urlXML' => $newOrder->urlXML ) );
                                    $where = 'id = ' . $newOrder->getId();
                                    $save->updateDB( 'ordenes_venta', null, $where );

                                    $this->createOpposingODC( $newOrder );
                                }
                            }
                        }
                    }

                    $db->transactionCommit();

                    $this->app->redirect( $redirectUrl, JText::_( 'LBL_ORDER_AUTHORIZED' ) );
                } else {
                    $this->app->redirect( $redirectUrl, JText::_( 'LBL_ORDER_NOT_AUTHORIZED' ), 'error' );
                }
            } catch (Exception $e) {
                $db->transactionRollback();

                $msg = $e->getMessage();
                JLog::add( $msg, JLog::ERROR, 'error' );
                $this->app->enqueueMessage( $msg, 'error' );
                $this->app->redirect( $redirectUrl );
            }
        } else {
            //acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    /**
     * @param $objOdv
     * @return array
     */
    public function sendEmail($objOdv)
    {
        /*
         *  NOTIFICACIONES 8
         */
        $getCurrUser     = new IntegradoSimple($this->integradoId);
        $titleArray      = array( $objOdv->numOrden);
        $numFact = Factura::getXmlUUID($objOdv->XML);
        $name = $getCurrUser->getDisplayName();

        $array           = array(
            $name,
            $numFact,
            JFactory::getUser()->username,
            date('d-m-Y'),
            '$'.number_format($objOdv->getTotalAmount(), 2),
            $objOdv->getReceptor()->getDisplayName(),
            $objOdv->numOrden);

        $send            = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]            = $send->sendNotifications('8', $array, $titleArray);

        /*
         * Notificaciones 9
         */

        $titleArrayAdmin = array( $name, $objOdv->numOrden );
        $arrayAdmin      = $array;

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('9', $arrayAdmin, $titleArrayAdmin);

        return $info;
    }

    public function getTotalAmount($productos){
        $totalAmount = 0;

        foreach ($productos as $producto) {
            if($producto->iva == 1){
                $producto->iva = 0;
            }
            if($producto->iva == 2){
                $producto->iva =11;
            }
            if($producto->iva == 3){
                $producto->iva = 16;
            }

            $total = ($producto->cantidad*$producto->p_unitario);
            $montoIva = $total*($producto->iva/100);
            $montoIeps = $total*($producto->ieps/100);

            $totalAmount = $total+$montoIva+$montoIeps+$totalAmount;
        }

        return $totalAmount;
    }

    /**
     * @param $odv
     *  Create ODC from ODV in case bith parties are Integrado
     * @return bool
     */
    private function createOpposingODC(OdVenta $odv) {

        if($odv->getReceptor()->isIntegrado()) {

            $save   = new sendToTimOne();
            $db     = JFactory::getDbo();

            $datos['integradoId'] = $this->integradoId;

            $odc = new OrdenFn();
            $odc->proyecto      = $odv->projectId2 != 0 ? $odv->projectId2 : $odv->projectId;
            $odc->proveedor     = $odc->getIdEmisor($odv, 'odv');
            $odc->integradoId   = $odc->getIdReceptor($odv, 'odv');
            $odc->numOrden      = $save->getNextOrderNumber('odc', $odc->integradoId);
            $odc->createdDate   = time();
            $odc->paymentDate   = $odv->paymentDate;
            $odc->paymentMethod = $odv->paymentMethod->id;
            $odc->totalAmount   = $odv->getTotalAmount();
            $odc->urlXML        = $odv->urlXML;
            $odc->observaciones = '';
            $odc->status        = 3;
            $odc->bankId        = $odv->account;


            $db->insertObject('#__ordenes_compra', $odc);

            $relation = new stdClass();
            $relation->id_odv = $odv->getId();
            $relation->id_odc = $db->insertid();

            $db->insertObject('#__ordenes_odv_odc_relation', $relation);

            $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->parametros) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');
        }
    }

}
