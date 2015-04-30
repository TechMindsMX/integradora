<?php
use Integralib\OrdenFn;

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
 * @property string returnUrl
 */
class MandatosControllerOdcpreview extends JControllerAdmin
{


    function __construct()
    {
        $post = array('idOrden' => 'INT');
        $this->app = JFactory::getApplication();
        $this->parametros = $this->app->input->getArray($post);
        $this->integradoId = Integrado::getSessionIntegradoIdOrRedirectWithError(JUri::getInstance());
        $this->comisiones = getFromTimOne::getComisionesOfIntegrado($this->integradoId);

        parent::__construct();
    }

    /**
     *
     */
    function authorize()
    {
        $db = JFactory::getDbo();
        $this->returnUrl = 'index.php?option=com_mandatos&view=odclist';;
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if ($this->permisos['canAuth']) {
            $integradoSimple = new IntegradoSimple($this->integradoId);
            $integradoSimple->getTimOneData();

            $this->checkSaldoSuficienteOrRedirectWithError($integradoSimple);

            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']      = (INT)$user->id;
            $this->parametros['authDate']    = time();
            $this->parametros['integradoId'] = (INT)$this->integradoId;

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'], 'odc_auth');
            $check = getFromTimOne::checkUserAuth($auths);

            if ($check) {
                $this->app->redirect($this->returnUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $odc        = getFromTimOne::getOrdenesCompra(null, $this->parametros['idOrden']);
            $odc        = $odc[0];
            $proveedor  = new IntegradoSimple(isset($odc->proveedor->integrado->integrado_id) ? $odc->proveedor->integrado->integrado_id : $odc->proveedor->id);
            $odvId      = OrdenFn::getRelatedOdvIdFromOdcId($odc->id);

	        if (!is_null($odvId)) {
		        $odv = \Integralib\OrderFactory::getOrder($odvId, 'odv');
		        if ( $odv->getStatus() != 5 ) {
			        $this->app->enquemessage(JText::_('ERR_416_AUTH_ODC'));
					$this->app->redirect($this->returnUrl);
		        }
	        }

            try{
                $db->transactionStart();
                $save->insertDB( 'auth_odc' );

                $auths       = OrdenFn::getCantidadAutRequeridas( new IntegradoSimple( $this->integradoId ), $odc->receptor );
                $numAutOrder = getFromTimOne::getOrdenAuths($odc->id, 'odc_auth');

                if($auths->emisor == count($numAutOrder)) {
                    $pagar = true;
                }else{
                    $save->changeOrderStatus($this->parametros['idOrden'],'odc',3);
                    $pagar = false;
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $db->transactionRollback();
                $this->app->redirect($this->returnUrl, 'no se pudo autorizar', 'error');
            }

            if($pagar){
                try {
                    $db->transactionStart();

                    $this->logEvents(__METHOD__, 'authorizacion_odc', json_encode($save->set));

                    $catalogoStatus = getFromTimOne::getOrderStatusCatalog();

                    $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 5);

                    $newStatusId = 13;
                    $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 13);

                    if ($proveedor->isIntegrado()) { //operacion de transfer entre integrados
                        $save->changeOrderStatus($odvId, 'odv', $newStatusId);
                    }

                    $this->txComision();
                    $this->realizaTx();

                    $db->transactionCommit();

                    $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED',  $catalogoStatus[$newStatusId]->name));
                    $this->app->enqueueMessage(JText::sprintf('ORDER_PAID_AUTHORIZED', $catalogoStatus[$newStatusId]->name));
                    $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_AUTHORIZED'));
                } catch (Exception $e) {
                    $db->transactionRollback();

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

    private function checkSaldoSuficienteOrRedirectWithError($integradoSimple)
    {
        if ($integradoSimple->timoneData->balance < $this->totalOperacionOdc()) {
            $this->app->redirect($this->returnUrl, 'ERROR_SALDO_INSUFICIENTE', 'error');
        }
    }

    /**
     * @return array
     */
    private function totalOperacionOdc()
    {
        $orden = getFromTimOne::getOrdenesCompra(null, $this->parametros['idOrden']);
        $orden = $orden[0];

        $montoComision = 0;
        if (isset($this->comisiones)) {
            $montoComision = getFromTimOne::calculaComision($orden, 'ODC', $this->comisiones);
        }

        $totalOperacion = (float)$orden->totalAmount + (float)$montoComision;

        return $totalOperacion;
    }

    private function realizaTx()
    {
        $orden = $this->getOrden();

        $idProveedor = $orden->receptor->id;

        $proveedor = new IntegradoSimple($idProveedor);

        if ($proveedor->isIntegrado()) { //operacion de transfer entre integrados
            $txData = new transferFunds($orden, $orden->integradoId, $proveedor->getId(), $orden->totalAmount);
            $txDone = $txData->sendCreateTx();
        } else {
            $txData = new Cashout($orden, $orden->integradoId, $orden->proveedor->id, $orden->totalAmount, array('accountId' => $orden->bankId));
            $txDone = $txData->sendCreateTx();
        }

        if (!$txDone) {
            $this->txComision(true);//Tx reverso del cobro de comisión (cuando falle la trasferencia de fondos)
            throw new Exception(JText::_('ERR_411_TRANSFERFUNDS_FAILED'));
        }

        $this->sendEmail($txData);
    }

    private function txComision($reverse = false)
    {
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden = $this->getOrden();
        $montoComision = getFromTimOne::calculaComision($orden, 'ODC', null);

        $orden->orderType = 'CCom-' . $orden->orderType;

        if (!is_null($montoComision)) {
            if (!$reverse) {
                $txComision = new transferFunds($orden, $orden->integradoId, 1, $montoComision);
            } else {
                $txComision = new transferFunds($orden, 1, $orden->integradoId, $montoComision);
            }

            if (!$txComision->sendCreateTx()) {
                throw new Exception(JText::_('ERR_412_TXCOMISION_FAILED'));
            }
        }
    }

    private function getOrden()
    {
        $orden = getFromTimOne::getOrdenesCompra(null, $this->parametros['idOrden']);
        $orden = $orden[0];

        return $orden;
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

        $odc = getFromTimOne::getOrdenesCompra($this->integradoId, $this->parametros['idOrden']);
        $odc = $odc[0];

        $info = array();

        $getCurrUser = new IntegradoSimple($this->integradoId);
        $titleArray = array($odc->numOrden);

        $array = array(
            $getCurrUser->getDisplayName(),
            $odc->numOrden,
            JFactory::getUser()->name,
            date('d-m-Y'),
            '$' . number_format($odc->totalAmount, 2),
            strtoupper($odc->receptor->getDisplayName()));

        $arrayNotificacion33 = array(
            $getCurrUser->getDisplayName(),
            $odc->numOrden,
            date('d-m-Y'),
            '$' . number_format($odc->totalAmount, 2),
            strtoupper($odc->receptor->getDisplayName()),
            $txData->orden->pastData
        );

        $send = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[] = $send->sendNotifications('14', $array, $titleArray);
        $info[] = $send->sendNotifications('33', $arrayNotificacion33, $titleArray);

        /*
         * Notificaciones 15&34
         */

        $titleArrayAdmin = array($getCurrUser->getDisplayName(), $odc->numOrden);

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('15', $array, $titleArrayAdmin);
        $info[] = $send->sendNotifications('34', $arrayNotificacion33, $titleArrayAdmin);

        return $info;
    }
}
