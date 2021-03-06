<?php
use Integralib\OdRetiro;
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.rutas');
jimport('integradora.notifications');
jimport('html2pdf.reportecontabilidad');


/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 * @property  txsToDo
 */
class MandatosControllerOdrpreview extends JControllerAdmin {

    protected $orden;
    protected $cashoutObj;
    protected $currentIntegrado;
    protected $txsToDo;

    function __construct( ) {
        $post                   = array( 'idOrden' => 'INT' );
        $this->app 			    = JFactory::getApplication();
        $this->parametros	    = $this->app->input->getArray($post);
        $this->integradoId      = JFactory::getSession()->get('integradoId', null, 'integrado');
        $this->comisiones       = getFromTimOne::getComisionesOfIntegrado($this->integradoId);
        $this->permisos         = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);
        $currentIntegrado       = new IntegradoSimple($this->integradoId);
        $currentIntegrado->getTimOneData();
        $this->currentIntegrado = $currentIntegrado;

        $orden                  = getFromTimOne::getOrdenesRetiro(null,$this->parametros['idOrden']);
        $this->orden            = $orden[0];

        parent::__construct(array());
    }

    function authorize() {
        if($this->permisos['canAuth']) {
            $enoughBalance                   = $this->enoughBalance();
            $user                            = JFactory::getUser();
            $save                            = new sendToTimOne();
            $this->parametros['userId']      = (INT)$user->id;
            $this->parametros['authDate']    = time();
            $this->parametros['integradoId'] = $this->integradoId;
            $db                              = JFactory::getDbo();

            $logdata                      = implode(
                ' | ',
                array(
                    JFactory::getUser()->id,
	                $this->integradoId,
                    __METHOD__,
                    json_encode( array($this->orden->id, $enoughBalance)
                    )
                )
            );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');

            $save->formatData($this->parametros);

            //Si no tiene Fondos se redirecciona.
            if (!$enoughBalance) {
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_INSUFFIENT_FUND'), 'error');
            }

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odr_auth');
            $check = getFromTimOne::checkUserAuth($auths, $this->integradoId);

            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            try{
                $db->transactionStart();

                $odr        = new OdRetiro(null, $this->parametros['idOrden']);
                $logdata    = implode(' | ',
                    array(JFactory::getUser()->id,
                          JFactory::getSession()->get('integradoId', null, 'integrado'),
                          __METHOD__,
                          json_encode( array($check, $this->parametros)
                          )
                    )
                );
                JLog::add($logdata, JLog::DEBUG, 'bitacora');

                $resultado = $save->insertDB('auth_odr');
                $auths     = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odr_auth');
                $authsReq  = OrdenFn::getCantidadAutRequeridas( new IntegradoSimple( $odr->getEmisor()->id ), new IntegradoSimple( $odr->getReceptor()->id ) );

                if($authsReq->emisor == count($auths)) {
                    $pagar = true;
                }else{
                    $save->changeOrderStatus($this->parametros['idOrden'],'odr',3);
                    $pagar = false;
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $db->transactionRollback();
                exit;
            }

            if($pagar){
                $db->transactionStart();
                try {
                    if ($resultado) {// autorización guardada
                        $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '5');

                        if ($statusChange) {// Se Cambio a estatus Autorizada
                            $cashOut = $this->cashout();

                            if ($cashOut) {// Se realizo en pago de la Orden
                                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '13');
                                $comision = $this->txComision();

                                $createPDF = new reportecontabilidad();
                                $createPDF->createPDF($this->tranfer, 'cashout');

                                if ($statusChange && $comision) { //Se realizo el cobro de la comision
                                    $this->app->enqueueMessage(JText::sprintf('ORDER_PAID', $this->orden->numOrden));

                                    $this->sendNotifications();

                                    $class = new reportecontabilidad();
                                    $class->createPDF($this->orden, 'odr');
                                }
                            } else {
                                throw new Exception( JText::_('ORDER_NO_PAID') );
                            }
                        }
                    }

                    $db->transactionCommit();

                    $this->app->redirect('index.php?option=com_mandatos&view=odrlist');
                }catch (Exception $e){
                    $db->transactionRollback();

                    $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '3');

                    $save->deleteDB('auth_odr', 'idOrden = ' . $this->parametros['idOrden'], ' AND userId = ' . JFactory::getUser()->id);

                    $this->app->redirect('index.php?option=com_mandatos&view=odrlist', $e->getMessage(), 'error');
                }
            }else{
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    public function sendNotifications() {

        /*
         * NOTIFICACIONES 25
         */

        $getCurrUser         = new IntegradoSimple($this->integradoId);
        $titleArray          = array($this->orden->numOrden);
        $array               = array($getCurrUser->getUserPrincipal()->name, $this->orden->numOrden, date('d-m-Y'), '$'.number_format($this->orden->totalAmount,2), $this->cuenta->banco_cuenta, $this->orden->numOrden);

        $send                   = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);
        $info[] = $send->sendNotifications('25', $array, $titleArray);

        /*
         * Notifications 26
         */

        $titleArrayAdmin        = array($getCurrUser->getUserPrincipal()->name, $this->orden->numOrden);

        $send                   = new Send_email();
        $send->setAdminEmails();
        $info[]                 = $send->sendNotifications('26', $array, $titleArrayAdmin);

    }

    private function enoughBalance() {

        $balance = $this->currentIntegrado->timoneData->balance;

        $orden = getFromTimOne::getOrdenesRetiro(null, $this->parametros['idOrden']);
        $orden = $orden[0];

        $montoComision = 0;
        if (isset($this->comisiones)) {
            $montoComision = getFromTimOne::calculaComision($orden, 'ODR', $this->comisiones);
        }

        $totalOperacion = (float)$orden->totalAmount + (float)$montoComision;

        if($balance >= $totalOperacion){
            $respuesta = true;
        }else{
            $respuesta = false;
        }

        return $respuesta;
    }

    private function cashout(){
        $this->orden->status->id = 5;

        if($this->orden->status->id == 5){
            $tranfer = new Cashout($this->orden, $this->orden->integradoId, $this->orden->integradoId, $this->orden->totalAmount, array('accountId' => $this->orden->cuenta->datosBan_id));
            $resultado = $tranfer->sendCreateTx();
        }
        $this->tranfer = $tranfer;
        return $resultado == 200;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $integradora    = new \Integralib\Integrado();
        $orden          = $this->orden;
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODR', $this->comisiones);

        $orden->orderType = 'CCom-'.$orden->orderType;

        $txComision     = new transferFunds($orden,$orden->integradoId,$integradora->getIntegradoraUuid(),$montoComision);

        return $txComision->sendCreateTx();
    }
}
