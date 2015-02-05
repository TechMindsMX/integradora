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
        $this->integradoId = Integrado::getSessionIntegradoIdOrRedirectWtihError(JUri::getInstance());
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
                            //mensaje no se cobro la comision;
                        }
                    }else{

                        $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odc', 3);

                        if($statusChange) {
                            $save->deleteDB('auth_odc', 'idOrden = ' . $this->parametros['idOrden']);
                            $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
                        }
                    }

                    /*if (isset($this->integradoId)) {
                        $integradoAdmin = new IntegradoSimple(93);

                        $titulo = JText::_('TITULO_13');
                        $titulo = str_replace('$idOrden', '<strong style="color: #000000">' . $this->parametros['idOrden'] . '</strong>', $titulo);

                        $contenido = JText::_('NOTIFICACIONES_13');

                        $contenido = str_replace('$integrado', '<strong style="color: #000000">' . $integradoSimple->user->username . '</strong>', $contenido);
                        $contenido = str_replace('$idOrden', '<strong style="color: #000000">' . $this->parametros['idOrden'] . '</strong>', $contenido);
                        $contenido = str_replace('$usuario', '<strong style="color: #000000">$' . JFactory::getUser()->username . '</strong>', $contenido);
                        $contenido = str_replace('$fecha', '<strong style="color: #000000">' . date('d-m-Y') . '</strong>', $contenido);

                        $data['titulo'] = $titulo;
                        $data['body'] = $contenido;
                        $data['email'] = JFactory::getUser()->email;

                        $send = new Send_email();
                        $send->notification($data);


                        $titulo = JText::_('TITULO_14');
                        $titulo = str_replace('$integrado', '<strong style="color: #000000">' . $integradoAdmin->user->username . '</strong>', $titulo);
                        $titulo = str_replace('$idOrden', '<strong style="color: #000000">' . $this->parametros['idOrden'] . '</strong>', $titulo);

                        $contenido = JText::_('NOTIFICACIONES_14');

                        $contenido = str_replace('$integrado', '<strong style="color: #000000">' . $integradoAdmin->user->username . '</strong>', $contenido);
                        $contenido = str_replace('$idOrden', '<strong style="color: #000000">' . $this->parametros['idOrden'] . '</strong>', $contenido);
                        $contenido = str_replace('$usuario', '<strong style="color: #000000">$' . JFactory::getUser()->username . '</strong>', $contenido);
                        $contenido = str_replace('$fecha', '<strong style="color: #000000">' . date('d-m-Y') . '</strong>', $contenido);

                        $data['titulo'] = $titulo;
                        $data['body'] = $contenido;
                        $data['email'] = $integradoAdmin->user->email;

                        $send = new Send_email();
                        $send->notification($data);
                    }*/

                }

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

        $proveedor = new IntegradoSimple($orden->proveedor);
        $rutas = new servicesRoute();


        if( !empty($proveedor->usuarios) ) { //operacion de transfer entre integrados
            $txData = new transferFunds($orden, $orden->integradoId, $orden->proveedor, $orden->totalAmount);
            $txDone = $txData->sendCreateTx();
        }else{
            $txData = new Cashout($orden,$orden->integradoId,$orden->proveedor,$orden->totalAmount, array('accountId' => $orden->bankId));
            $txDone = $txData->sendCreateTx();
        }

        return $txDone;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden = $this->getOrden();
        $montoComision = getFromTimOne::calculaComision($orden, 'ODC', $this->comisiones);

        $txComision = new transferFunds($orden,$orden->integradoId,1,$montoComision);

        return $txComision->sendCreateTx();
    }

    private function getOrden(){
        $orden = getFromTimOne::getOrdenes(null, $this->parametros['idOrden'], 'ordenes_compra');
        $orden = $orden[0];

        return $orden;
    }

    function logEvents($metodo, $info, $data){
        $logdata = $logdata = implode(', ',array(
            JFactory::getUser()->id,
            JFactory::getSession()->get('integradoId', null, 'integrado'),
            $metodo,
            json_encode( array($info, $data) )
            )
        );
        JLog::add($logdata, JLog::DEBUG, 'bitacora_auth');
    }
}
