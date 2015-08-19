<?php
use Integralib\OdVenta;
use Integralib\OrdenFn;
use Integralib\OrderFactory;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.notifications');
jimport('phpqrcode.qrlib');

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 */
class MandatosControllerOdvpreview extends JControllerLegacy {

    function authorize() {
        $db = JFactory::getDbo();
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
            $order                              = new OdVenta(null, $this->parametros['idOrden']);

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odv_auth');

            $check = getFromTimOne::checkUserAuth($auths, $this->integradoId);

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            try{
                $db->transactionStart();
                $resultado   = $save->insertDB( 'auth_odv',null,null,true);
                $auths       = OrdenFn::getCantidadAutRequeridas(new IntegradoSimple( OrdenFn::getIdEmisor($order, 'odv') ), new IntegradoSimple( OrdenFn::getIdReceptor($order, 'odv') ));
                $numAutOrder = getFromTimOne::getOrdenAuths($order->getId(), 'odv_auth');

                if($auths->emisor == count($numAutOrder)) {
                    $pagar = true;
                }else{
                    $save->changeOrderStatus($this->parametros['idOrden'],'odv',3);
                    $pagar = false;
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $db->transactionRollback();
                $this->app->redirect($redirectUrl, 'no se pudo autorizar', 'error');
            }

            if($pagar){
                try {
                    $db->transactionStart();

                    if ($resultado != false) {
                        // autorización guardada
                        $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                        $newStatusId    = 5;

                        $save->changeOrderStatus($this->parametros['idOrden'], 'odv', $newStatusId);


                        $newOrder = OrderFactory::getOrder($this->parametros['idOrden'], 'odv');

                        if ($newOrder->getStatus()->id == 5 && $newOrder->urlXML == '') {
                            $receptor = $newOrder->getReceptor();
                            $timbrar = $receptor->isIntegrado() ? false : true;

                            $factObj = $save->generaObjetoFactura($newOrder, $timbrar);

                            if ($factObj != false) {
                                $xmlFactura = $save->generateFacturaFromTimone($factObj);

                                try {
	                                $newOrder->urlXML = $save->saveXMLFile($xmlFactura);
	                                $factObj->saveFolio($xmlFactura);
	                                $newOrder->XML = $xmlFactura;
                                    if($timbrar){

                                        //Codigo QR
                                        $xml = new xml2Array();
                                        $factura = $xml->manejaXML($xmlFactura);

                                        $qrData = '?re='.$factura->emisor['attrs']['RFC'].'&rr='.$factura->receptor['attrs']['RFC'].'&tt='.$factura->comprobante['TOTAL'].'&id='.$factura->complemento['children'][0]['attrs']['UUID'];
                                        $tmpPath = JPATH_BASE.'/media/qrcodes';

                                        $filename = $newOrder->createdDate.'-'.$this->integradoId.'-'.$newOrder->id.'.png';
                                        $pngPath = $tmpPath.'/'.$filename;

                                        QRcode::png($qrData,$pngPath);
                                        if(file_exists($pngPath)){
                                            $saveqrname = new stdClass();

                                            $saveqrname->integradoId = $newOrder->integradoId;
                                            $saveqrname->qrName      = $filename;
                                            $saveqrname->createdDate = time();

                                            $db->insertObject('#__integrado_pdf_qr',$saveqrname);
                                        }
                                        //fin codigo qr
                                    }
                                    $info = $this->sendEmail($newOrder);

                                } catch (Exception $e) {
                                    $msg = $e->getMessage();
                                    JLog::add($msg, JLog::ERROR, 'error');
                                    $this->app->enqueueMessage($msg, 'error');
                                }
                            }

                            if (isset($newOrder->urlXML)) {
                                if ($newOrder->urlXML != false) {
                                    $save->formatData(array('urlXML' => $newOrder->urlXML));
                                    $where = 'id = ' . $newOrder->getId();
                                    $save->updateDB('ordenes_venta', null, $where);

                                    $this->createOpposingODC($newOrder);
                                }
                            }
                        }else{
                            $idOdcRelated = OrdenFn::getRelatedOdcIdFromOdvId($newOrder->getId());

                            $changeStatusOdc = new stdClass();
                            $changeStatusOdc->id = $idOdcRelated;
                            $changeStatusOdc->status = 3;

                            $db->updateObject('#__ordenes_compra',$changeStatusOdc, 'id');
                        }

                        $db->transactionCommit();
                        $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));
                        $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED'));
                    } else {
                        $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
                    }
                } catch (Exception $e) {
                    $db->transactionRollback();
                    $save->deleteDB('auth_odv',$db->quoteName('id').' = '.$db->quote($resultado));
                    $msg = $e->getMessage();

                    JLog::add($msg, JLog::ERROR, 'error');
                    $this->app->enqueueMessage($msg, 'error');
                    $this->app->redirect($redirectUrl);
                }
            }else{
                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZE_STANDBY'), 'error');
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
            $odc->proyecto      = $odv->subproyecto == '' ? $odv->proyecto->id_proyecto : $odv->subproyecto->id_proyecto;
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
