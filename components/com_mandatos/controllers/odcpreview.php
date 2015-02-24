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
 * @property string returnUrl
 */
class MandatosControllerOdcpreview extends JControllerAdmin
{


    function __construct(){
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
    function authorize(){

        $this->returnUrl = 'index.php?option=com_mandatos&view=odclist';;
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if ($this->permisos['canAuth']) {
            $integradoSimple = new IntegradoSimple($this->integradoId);
            $integradoSimple->getTimOneData();
            $integradoSimple->timoneData->balance = (float)10000000;

            $this->checkSaldoSuficienteOrRedirectWithError($integradoSimple);

            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId'] = (INT)$user->id;
            $this->parametros['authDate'] = time();

            unset($this->parametros['integradoId']);

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'], 'odc_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if ($check) {
                $this->app->redirect('index.php?option=com_mandatos&view=odclist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }
            $resultado = $save->insertDB('auth_odc');

            if ($resultado) {
                $this->logEvents(__METHOD__,'authorizacion_odc',json_encode($save->set));

                $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                $newStatusId = 5;

                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odc', $newStatusId);
                if ($statusChange) {

                    $TxOdc = $this->realizaTx();

                    if($TxOdc){
                        $cobroComision = $this->txComision();

                        if($cobroComision) {
                            $newStatusId = 13;
                            $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odc', $newStatusId);

                            if($statusChange){
                                $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));
                            }
                        }else{
                            $this->app->enqueueMessage(JText::sprintf('ORDER_PAID_AUTHORIZED', $catalogoStatus[$newStatusId]->name));
                        }
                    }else{

                        $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 3);

                        if($statusChange) {
                            $save->deleteDB('auth_odc', 'idOrden = ' . $this->parametros['idOrden']);
                            $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
                        }
                    }
                }

                $this->sendEmail();
                $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_AUTHORIZED'));
            } else {
                $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($this->returnUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    private function checkSaldoSuficienteOrRedirectWithError($integradoSimple){
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

    private function realizaTx(){
        $orden = $this->getOrden();

        $proveedor = new IntegradoSimple($orden->proveedor->id);

        if( !empty($proveedor->usuarios) ) { //operacion de transfer entre integrados
            $txData = new transferFunds($orden, $orden->integradoId, $orden->proveedor->id, $orden->totalAmount);
            $txDone = $txData->sendCreateTx();
        }else{
            $txData = new Cashout($orden,$orden->integradoId,$orden->proveedor->id,$orden->totalAmount, array('accountId' => $orden->bankId));
            $txDone = $txData->sendCreateTx();
        }

        return $txDone;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->getOrden();
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODC', $this->comisiones);
        $respuesta      = true;

        if(!is_null($montoComision)) {
            $orden->orderType = 'Cobro Comisión';

            $txComision = new transferFunds($orden, $orden->integradoId, 1, $montoComision);
            $respuesta = $txComision->sendCreateTx();
        }


        return $respuesta;
    }

    private function getOrden(){
        $orden = getFromTimOne::getOrdenesCompra(null, $this->parametros['idOrden']);
        $orden = $orden[0];

        return $orden;
    }

    function logEvents($metodo, $info, $data){
        $logdata = implode(' | ',array(
            JFactory::getUser()->id,
            JFactory::getSession()->get('integradoId', null, 'integrado'),
            $metodo,
            json_encode( array($info, $data) )
            )
        );
        JLog::add($logdata, JLog::DEBUG, 'bitacora_auth');
    }

    public function sendEmail()
    {
        /*
         *  NOTIFICACIONES 14&33
         */

        $odc = getFromTimOne::getOrdenesCompra($this->integradoId, $this->parametros['idOrden']);

        $info = array();

        $getCurrUser     = new IntegradoSimple($this->integradoId);
        $titleArray      = array( $this->parametros['idOrden']);

        $array           = array($getCurrUser->user->name, $this->parametros['idOrden'], JFactory::getUser()->username, date('d-m-Y'), $odc[0]->totalAmount, $odc[0]->proveedor->corporateName);

        $send            = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]            = $send->sendNotifications('14', $array, $titleArray);

        /*
         * Notificaciones 15&34
         */

        $titleArrayAdmin = array( $getCurrUser->user->username, $this->parametros['idOrden'] );

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('15', $array, $titleArrayAdmin);

        return $info;
    }
}
