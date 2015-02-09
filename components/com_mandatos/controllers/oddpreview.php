<?php
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
class MandatosControllerOddpreview extends JControllerAdmin {

    private $integradoId;
    private $orden;

    function authorize() {
        $this->app 			= JFactory::getApplication();
        $this->parametros['idOrden']	= $this->app->input->get('idOrden', null, 'INT');

        $session = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null,'integrado');

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odd_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=oddlist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $resultado = $save->insertDB('auth_odd');

            if($resultado) {
                // autorización guardada

                $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                $newStatusId  = 5;
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odd', $newStatusId);
	            if ($statusChange){
		            $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));

                    $orden       = getFromTimOne::getOrdenesDeposito(null, $this->parametros['idOrden']);
                    $this->orden = $orden[0];
                    $this->sendNotifications();

                }

                $this->app->redirect('index.php?option=com_mandatos&view=oddlist', JText::_('LBL_ORDER_AUTHORIZED'));
            }else{
                $this->app->redirect('index.php?option=com_mandatos&view=oddlist', JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect('index.php?option=com_mandatos&view=oddlist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    private function sendNotifications( ) {

        $info           = array();
        /*
         * NOTIFICACIONES 31
         */

        $getCurrUser         = new IntegradoSimple($this->integradoId);

        $titleArray          = array($this->orden->numOrden);
        $array               = array($getCurrUser->getUserPrincipal()->name, $this->orden->numOrden,  JFactory::getUser()->username, date('d-m-Y'), $this->orden->totalAmount, $this->orden->paymentMethod->name);

        $send                = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]              = $send->sendNotifications('31', $array, $titleArray);

        /*
         * NOTIFICACIONES 32
         */

        $titleArrayAdmin     = array($getCurrUser->getUserPrincipal()->name, $this->orden->numOrden);

        $send->setAdminEmails();
        $info[]             = $send->sendNotifications('32', $array, $titleArrayAdmin);
    }

    private function logEvent( $info, $dato ) {
        $logdata = $logdata = implode( ', ', array (
            JFactory::getUser()->id,
            $this->integradoId,
            __METHOD__.':'.__LINE__,
            json_encode( array ( $info, $dato  ) )
        ) );
        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

    }
}
