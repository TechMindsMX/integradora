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

            $this->cashoutObj =  new Cashout($this->orden, $this->orden->integradoId, $this->orden->integradoId, $this->orden->totalAmount, array('paymentMethod' => $this->orden->paymentMethod, 'accountId' => $this->orden->cuentaId  ));

            $this->txsToDo = $this->calculoComisionesOrdenRetiro();

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

                $cashOut = $this->cashout();
                if($cashOut){
                    $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '13');
                    if ($statusChange){
                        $this->app->enqueueMessage(JText::_('ORDER_PAID'));

                        $this->sendNotifications();
                    }
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
            $result = $this->cashoutObj->sendCreateTx();

            foreach ( $this->txsToDo as $txAmount ) {
                // TODO: traer el id de integradora de la db
                $tranfer = new transferFunds($this->orden, $this->orden->integradoId, 1, $txAmount);
                $tranfer->sendCreateTx();
            }


            return $result;
        }
    }

    public function sendNotifications() {
        /*NOTIFICACIONES 23*/
        $getCurrUser         = new IntegradoSimple($this->integradoId);

        $emails = array($getCurrUser->getUserPrincipal()->email, JFactory::getUser()->email, 'ricardolyon@gmail.com');
        $emails = array_unique($emails);

        $titulo = JText::_('TITULO_23');

        $contenido = JText::_('NOTIFICACIONES_23');

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $dato['email']          = $emails;
        $send                   = new Send_email();
        $info = $send->notification($dato);

        $integradoAdmin     = new IntegradoSimple(93);

        $titulo = JText::_('TITULO_24');

        $contenido = JText::_('NOTIFICACIONES_24');

        $datoAdmin['titulo']         = $titulo;
        $datoAdmin['body']           = $contenido;
        $datoAdmin['email']          = $integradoAdmin->user->email;
        $send                   = new Send_email();
        $infoAdmin = $send->notification($datoAdmin);

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
}
