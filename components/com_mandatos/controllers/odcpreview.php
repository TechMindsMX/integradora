<?php
use Integralib\IntFactory;
use Integralib\OdCompra;
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.notifications');
jimport('phpqrcode.qrlib');
jimport('html2pdf.PdfsIntegradora');
/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 * @property string returnUrl
 */
class MandatosControllerOdcpreview extends JControllerAdmin
{

    protected $odc;
    protected $currentIntegrado;

    function __construct(){
        $post = array('idOrden' => 'INT');
        $this->app = JFactory::getApplication();
        $this->parametros = $this->app->input->getArray($post);
        $this->integradoId = Integrado::getSessionIntegradoIdOrRedirectWithError(JUri::getInstance());
        $this->comisiones = getFromTimOne::getComisionesOfIntegrado($this->integradoId);

        parent::__construct();
    }

    function authorize()
    {
        $db = JFactory::getDbo();
        $this->returnUrl = 'index.php?option=com_mandatos&view=odclist';;
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if ($this->permisos['canAuth']) {
            $this->odc = new OdCompra(null, $this->parametros['idOrden']);
            $this->currentIntegrado = new IntegradoSimple($this->integradoId);
            $this->currentIntegrado->getTimOneData();

            try{
                $this->currentIntegrado->checkSaldoSuficiente($this->totalOperacionOdc());
            }catch (Exception $e){
                $this->app->enqueueMessage($e->getMessage(), 'ERROR');
                $this->app->redirect('index.php?option=com_mandatos&view=odclist');
            }

            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']      = (INT)$user->id;
            $this->parametros['authDate']    = time();
            $this->parametros['integradoId'] = (STRING)$this->integradoId;

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'], 'odc_auth');
            $check = getFromTimOne::checkUserAuth($auths, $this->integradoId);

            if ($check) {
                $this->app->redirect($this->returnUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $odvId      = OrdenFn::getRelatedOdvIdFromOdcId($this->odc->getId());

	        if (!is_null($odvId)) {
		        $odv = \Integralib\OrderFactory::getOrder($odvId, 'odv');

		        if ( $odv->getStatus()->id != 5 ) {
			        $this->app->enqueueMessage(JText::_('ERR_416_AUTH_ODC'), 'error');
					$this->app->redirect($this->returnUrl);
		        }
	        }

            $db->transactionStart();

            try{
                $save->insertDB( 'auth_odc',null,null,true );

                $auths       = OrdenFn::getCantidadAutRequeridas( $this->odc->getEmisor(), $this->odc->getReceptor() );
                $numAutOrder = getFromTimOne::getOrdenAuths($this->odc->getId(), 'odc_auth');

                if($auths->emisor == count($numAutOrder)) {
                    $pagar = true;

                    if (isset($odv)) {

                        if ($odv->hasToCreateInvoice()) {
                            $factObj = $save->generaObjetoFactura($odv);

                            if ($factObj != false) {
                                $xmlFactura = $save->generateFacturaFromTimone($factObj);

                                try {
                                    $urlXML = $save->saveXMLFile($xmlFactura);
                                    $dataUpdate = new stdClass();

                                    $dataUpdate->id = $this->odc->getId();
                                    $dataUpdate->urlXML = $urlXML;

                                    $db->updateObject('#__ordenes_compra', $dataUpdate, 'id');

                                    $odvUpdate = new stdClass();
                                    $odvUpdate->id = $odv->getId();
                                    $odvUpdate->urlXML = $urlXML;

                                    $db->updateObject('#__ordenes_venta', $odvUpdate, 'id');

                                    $createPDF = new PdfsIntegradora();
                                    $createPDF->createPDF($this->odc->getId(), 'odc');
                                    if($createPDF){
                                        $save->updateDB('ordenes_compra', array('urlPDFOrden = "'.$createPDF->path.'"'), 'id = '.$this->odc->getId());
                                    }

                                    //Codigo QR
                                    $xml = new xml2Array();
                                    $factura = $xml->manejaXML($xmlFactura);

                                    $qrData = '?re='.$factura->emisor['attrs']['RFC'].'&rr='.$factura->receptor['attrs']['RFC'].'&tt='.$factura->comprobante['TOTAL'].'&id='.$factura->complemento['children'][0]['attrs']['UUID'];
                                    $tmpPath = JPATH_BASE.'/media/qrcodes';

                                    $filename = $odv->createdDate.'-'.$odv->integradoId.'-'.$odvId.'.png';
                                    $pngPath = $tmpPath.'/'.$filename;

                                    QRcode::png($qrData,$pngPath);
                                    if(file_exists($pngPath)){
                                        $saveqrname = new stdClass();

                                        $saveqrname->integradoId = $odv->integradoId;
                                        $saveqrname->qrName      = $filename;
                                        $saveqrname->createdDate = time();

                                        $namePdfCreated = $createPDF->facturaPDF($factura, $odv, $factObj, $urlXML);

                                        $saveqrname->pdfName = $namePdfCreated;
                                        $save->updateDB('ordenes_compra', array('urlPDF = "'.$namePdfCreated.'"'), 'id = '.$this->odc->getId());

                                        $db->insertObject('#__integrado_pdf_qr',$saveqrname);
                                    }
                                    //fin codigo qr

                                    $factObj->saveFolio($xmlFactura);

                                } catch (Exception $e) {
                                    $msg = $e->getMessage();
                                    JLog::add($msg, JLog::ERROR, 'error');
                                    $this->app->enqueueMessage($msg, 'error');
                                }
                            }
                        }
                    }
                }
                elseif ($auths->emisor > count($numAutOrder) && count($numAutOrder) != 0) {
                    $this->app->enqueueMessage(JText::_('LBL_ORDER_AUTHORIZED'));
                    $this->app->enqueueMessage(JText::_('LBL_ORDER_NEED_MORE_AUTHS'));
                    $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 3);
                }
                else{
                    throw new Exception('AUTHS: '. $auths->emisor .', '. count($numAutOrder) );
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $msg = $e->getMessage();
                JLog::add($msg, JLog::ERROR, 'error');

                $db->transactionRollback();
                $pagar = false;
                $save->changeOrderStatus($this->parametros['idOrden'],'odc',3);
                $this->app->redirect($this->returnUrl, 'no se pudo autorizar', 'error');
            }

            if($pagar){
                $db->transactionStart();
                try {
                    $this->logEvents(__METHOD__, 'authorizacion_odc', json_encode($save->set));

                    $catalogoStatus = getFromTimOne::getOrderStatusCatalog();

                    $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 5);

                    if( $this->odc->paymentMethod->id === 1 ){
                        $this->txComision();
                        $this->realizaTx();

                        $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 13);

                        if ($this->odc->getReceptor()->isIntegrado()) { //operacion de transfer entre integrados
                            $save->changeOrderStatus($odvId, 'odv', 13);
                        }

                        $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED',  $catalogoStatus[13]->name));
                        $this->app->enqueueMessage(JText::sprintf('ORDER_PAID_AUTHORIZED', $catalogoStatus[13]->name));
                    }else{

                        $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED',  $catalogoStatus[5]->name));
                    }

                    $db->transactionCommit();
                    $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_AUTHORIZED'));
                } catch (Exception $e) {
                    $db->transactionRollback();

                    $save->changeOrderStatus($this->parametros['idOrden'],'odc',3);
                    $save->deleteDB('auth_odc', 'idOrden = '.$this->odc->getId().' AND integradoId = '.$db->quote($this->integradoId));

                    $msg = $e->getMessage();
                    JLog::add($msg, JLog::ERROR, 'error');

                    $this->app->enqueueMessage($msg, 'error');
                    $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED', 'error'));
                }
            }else{
                $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_AUTHORIZE_STANDBY', 'error'));
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($this->returnUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    /**
     * @return array
     */
    private function totalOperacionOdc()
    {
        $montoComision = 0;
        if (isset($this->comisiones)) {
            $montoComision = $this->odc->calculaComision($this->comisiones);
        }

        $totalOperacion = (float)$this->odc->getTotalAmount() + (float)$montoComision;

        return $totalOperacion;
    }

    private function realizaTx()
    {
        if ($this->odc->getReceptor()->isIntegrado()) { //operacion de transfer entre integrados
            $txData = new transferFunds($this->odc, $this->odc->getEmisor(), $this->odc->getReceptor(), $this->odc->getTotalAmount());
            $txDone = $txData->sendCreateTx();
        } else {

            $txData = new Cashout($this->odc, $this->odc->getEmisor(), $this->odc->getReceptor(), $this->odc->getTotalAmount(), array('accountId' => $this->odc->bankId));
            $txDone = $txData->sendCreateTx();

            if($txDone){
                $createPDF = new PdfsIntegradora();
                $createPDF->createPDF($txData, 'cashout');
            }
        }

        if (!$txDone) {
            $this->txComision(true);//Tx reverso del cobro de comisión (cuando falle la trasferencia de fondos)
            throw new Exception(JText::_('ERR_411_TRANSFERFUNDS_FAILED'));
        }

        $this->sendEmail($txData);
    }

    private function txComision($reverse = false)
    {
        $saveTx = true;
        $integradora = new \Integralib\Integrado();

        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $montoComision = getFromTimOne::calculaComision($this->odc, 'ODC', $this->comisiones);

        if (!is_null($montoComision)) {
            if (!$reverse) {
                $txComision = new transferFunds($this->odc, $this->odc->getEmisor(), IntFactory::getIntegradoSimple($integradora->getIntegradoraUuid()), $montoComision, true);
            } else {
                $txComision = new transferFunds($this->odc, IntFactory::getIntegradoSimple($integradora->getIntegradoraUuid()), $this->odc->getEmisor(), $montoComision, true);
                $saveTx = false;
            }

            if (!$txComision->sendCreateTx($saveTx)) {
                throw new Exception(JText::_('ERR_412_TXCOMISION_FAILED'));
            }
        }
    }

    function logEvents($metodo, $info, $data)
    {
        $logdata = implode(' | ', array(
                JFactory::getUser()->id,
                JFactory::getSession()->get('integradoId', null, 'integrado'),
                $metodo,
                json_encode(array($info, $data))
            )
        );
        JLog::add($logdata, JLog::DEBUG, 'bitacora_auth');
    }

    public function sendEmail($txData)
    {
        /*
         *  NOTIFICACIONES 14&33
         */

        $info = array();

        $titleArray = array($this->odc->numOrden);

        $array = array(
            $this->currentIntegrado->getDisplayName(),
            $this->odc->numOrden,
            JFactory::getUser()->name,
            date('d-m-Y'),
            '$' . number_format($this->odc->getTotalAmount(), 2),
            strtoupper($this->odc->getReceptor()->getDisplayName()));

        $arrayNotificacion33 = array(
            $this->currentIntegrado->getDisplayName(),
            $this->odc->numOrden,
            date('d-m-Y'),
            '$' . number_format($this->odc->getTotalAmount(), 2),
            strtoupper($this->odc->getReceptor()->getDisplayName()),
            $txData->orden->pastData
        );

        $send = new Send_email();

        $send->setIntegradoEmailsArray($this->currentIntegrado);
        $info[] = $send->sendNotifications('14', $array, $titleArray);
        $info[] = $send->sendNotifications('33', $arrayNotificacion33, $titleArray);

        /*
         * Notificaciones 15&34
         */

        $titleArrayAdmin = array($this->currentIntegrado->getDisplayName(), $this->odc->numOrden);

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('15', $array, $titleArrayAdmin);
        $info[] = $send->sendNotifications('34', $arrayNotificacion33, $titleArrayAdmin);

        return $info;
    }
}
