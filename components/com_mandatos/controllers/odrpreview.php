<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.rutas');
jimport('integradora.notifications');

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
            $enoughBalance                = $this->enoughBalance();
            $user                         = JFactory::getUser();
            $save                         = new sendToTimOne();
            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();
            $save->formatData($this->parametros);
            $auths                        = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odr_auth');
            $check                        = getFromTimOne::checkUserAuth($auths);

            $logdata = implode(
                ' | ',
                array(
                    JFactory::getUser()->id,
                    JFactory::getSession()->get('integradoId', null, 'integrado'),
                    __METHOD__,
                    json_encode( array($this->orden->id, $enoughBalance)
                    )
                )
            );

            JLog::add($logdata, JLog::DEBUG, 'bitacora');

            //Si no tiene Fondos se redirecciona.
            if (!$enoughBalance) {
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_INSUFFIENT_FUND'), 'error');
            }


            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($check, $this->parametros) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');

            $resultado = $save->insertDB('auth_odr');

            if($resultado) {
                // autorización guardada
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '5');

                if($statusChange) {
                    $cashOut = $this->cashout();

                    if ($cashOut) {
                        $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '13');
                        $comision = $this->txComision();

                        if ($statusChange && $comision) {
                            $this->app->enqueueMessage(JText::_('ORDER_PAID'));

                            $this->sendNotifications();
                        }
                    } else {
                        $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '3');

                        if($statusChange) {
                            $save->deleteDB('flpmu_auth_odr', 'idOrden = ' . $this->parametros['idOrden'], ' AND userId = ' . JFactory::getUser()->id);
                        }else{
                            $this->app->enqueueMessage(JText::_('ERROR_PLATAFORM_CONTACT_ADMIN'));
                        }
                        $this->app->enqueueMessage(JText::_('ORDER_NO_PAID'));
                    }

                    $this->app->redirect('index.php?option=com_mandatos&view=odrlist');
                }else{
                    $this->app->enqueueMessage(JText::_('ORDER_NOT_STATUS_CHANGED'));
                    $this->app->redirect('index.php?option=com_mandatos&view=odrlist');
                }
            }else{
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
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
            $tranfer->sendCreateTx();
        }
        return $tranfer;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->orden;
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODR', $this->comisiones);

        $orden->orderType = 'CCom-'.$orden->orderType;

        $txComision     = new transferFunds($orden,$orden->integradoId,1,$montoComision);

        return $txComision->sendCreateTx();
    }
}
