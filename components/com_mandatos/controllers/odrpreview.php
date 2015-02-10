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
        $this->integradoId = JFactory::getSession()->get('integradoId', null, 'integrado');
        $this->comisiones = getFromTimOne::getComisionesOfIntegrado($this->integradoId);

        parent::__construct(array());
    }

    function authorize() {
        $post               = array( 'idOrden' => 'INT' );
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );
        $currentIntegrado = new IntegradoSimple($this->integradoId);
        $currentIntegrado->getTimOneData();
        $this->currentIntegrado = $currentIntegrado;

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);
        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar

            $orden = getFromTimOne::getOrdenesRetiro(null,$this->parametros['idOrden']);
            $this->orden = $orden[0];

            $enoughBalance = $this->enoughBalance();

            $logdata = $logdata = implode(', ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->orden->id, $enoughBalance) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');

            if (!$enoughBalance) {
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_INSUFFIENT_FUND'), 'error');
            }

            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odr_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $logdata = $logdata = implode(', ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($check, $this->parametros) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');

            $resultado = $save->insertDB('auth_odr');

            if($resultado) {
                // autorización guardada
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '5');
                if ($statusChange){
                    $this->app->enqueueMessage(JText::_('LBL_ORDER_AUTHORIZED'));
                }

                $orden = getFromTimOne::getOrdenesRetiro(null,$this->parametros['idOrden']);
                $this->orden = $orden[0];

                $cashOut = $this->cashout();
                if($cashOut){
                    $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '13');
                    $comision = $this->txComision();

                    if ($statusChange && $comision){
                        $this->app->enqueueMessage(JText::_('ORDER_PAID'));

                        $this->sendNotifications();
                    }
                }else{
                    $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '3');
                    $save->deleteDB('flpmu_auth_odr', 'idOrden = '.$this->parametros['idOrden'],' AND userId = '.JFactory::getUser()->id);
                }

                $this->app->redirect('index.php?option=com_mandatos&view=odrlist');
            }else{
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    private function cashout(){
        //cashOut si cambia al 5

        if($this->orden->status->id == 5){

            // TODO: traer el id de integradora de la db

            $tranfer = new Cashout($this->orden, $this->orden->integradoId, $this->orden->integradoId, $this->orden->totalAmount, array('accountId' => $this->orden->cuenta->datosBan_id));
            $tranfer->sendCreateTx();

            return $tranfer;
        }
    }

    public function sendNotifications() {

        /*
         * NOTIFICACIONES 25
         */


        var_dump($this);exit;
        $getCurrUser         = new IntegradoSimple($this->integradoId);
        $titleArray          = array($this->orden->numOrden);
        $array               = array($getCurrUser->getUserPrincipal()->name, $this->orden->numOrden, date('d-m-Y'), $this->orden->totalAmount, $this->cuenta->banco_cuenta, $this->orden->numOrden);

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

    private function calculoComisionesOrdenRetiro() {
        $comisiones = getFromTimOne::getComisionesOfIntegrado( $this->integradoId );

        $txs['montoComisionOrden'] = getFromTimOne::calculaComision( $this->orden, 'ODR', $comisiones );

        $txs['montoComisionFijaTx'] = $this->cashoutObj->getComisionFijaTx();

        return $txs;
    }

    private function enoughBalance() {

        $balance = $this->currentIntegrado->timoneData->balance;
        $totalOrden = $this->orden->totalAmount + array_sum($this->txsToDo);

        return $balance >= $totalOrden;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->orden;
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODR', $this->comisiones);

        $orden->orderType = 'Cobro Comisión';

        $txComision     = new transferFunds($orden,$orden->integradoId,1,$montoComision);

        return $txComision->sendCreateTx();
    }

}
