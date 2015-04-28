<?php
use Integralib\OdCompra;
use Integralib\OdVenta;
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

//            $this->checkSaldoSuficienteOrRedirectWithError($integradoSimple);

            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId'] = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'], 'odc_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if ($check) {
                $this->app->redirect('index.php?option=com_mandatos&view=odclist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $db = JFactory::getDbo();
            try {
                $db->transactionStart();

                $save->insertDB( 'auth_odc' );

                $this->logEvents( __METHOD__, 'authorizacion_odc', json_encode( $save->set ) );

                $catalogoStatus = getFromTimOne::getOrderStatusCatalog();

                $save->changeOrderStatus( $this->parametros['idOrden'], 'odc', 5 );

                $newStatusId  = 13;
                $save->changeOrderStatus( $this->parametros['idOrden'], 'odc', 13 );


                $odc       = getFromTimOne::getOrdenesCompra( null, $this->parametros['idOrden'] );
                $odc       = $odc[0];
                $proveedor = new IntegradoSimple( isset($odc->proveedor->integrado->integrado_id) ? $odc->proveedor->integrado->integrado_id : $odc->proveedor->id );

                if ( $proveedor->isIntegrado() ) { //operacion de transfer entre integrados
                    $odvId           = OrdenFn::getRelatedOdvIdFromOdcId( $odc->id );
                    if(is_null($odvId)){
                        $this->createOpossingOdv(new OdCompra(null,$odc->id));
                    }else {
                        $save->changeOrderStatus($odvId, 'odv', $newStatusId);
                    }
                }

                $this->app->enqueueMessage( JText::sprintf( 'ORDER_STATUS_CHANGED',
                    $catalogoStatus[ $newStatusId ]->name ) );
                $this->app->enqueueMessage( JText::sprintf( 'ORDER_PAID_AUTHORIZED',
                    $catalogoStatus[ $newStatusId ]->name ) );


                $this->txComision();
                $this->realizaTx();

                $this->sendEmail();
                $db->transactionCommit();

                $this->app->redirect( $this->returnUrl, JText::_( 'LBL_ORDER_AUTHORIZED' ) );
            } catch (Exception $e) {
                $db->transactionRollback();

                $msg = $e->getMessage();
                JLog::add( $msg, JLog::ERROR, 'error' );
                $this->app->enqueueMessage( $msg, 'error' );
//                $this->app->redirect( $this->returnUrl, JText::_( 'LBL_ORDER_NOT_AUTHORIZED', 'error' ) );

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

        $idProveedor = $orden->receptor->id;

        $proveedor = new IntegradoSimple($idProveedor);

        if( $proveedor->isIntegrado() ) { //operacion de transfer entre integrados
            $txData = new transferFunds($orden, $orden->integradoId, $proveedor->getId(), $orden->totalAmount);
            $txDone = $txData->sendCreateTx();
        }else{
            $txData = new Cashout($orden,$orden->integradoId,$orden->proveedor->id,$orden->totalAmount, array('accountId' => $orden->bankId));
            $txDone = $txData->sendCreateTx();
        }

        if(!$txDone){
            $this->txComision(true);//Tx reverso del cobro de comisión (cuando falle la trasferencia de fondos)
            throw new Exception(JText::_('ERR_411_TRANSFERFUNDS_FAILED'));
        }

    }

    private function txComision($reverse = false){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->getOrden();
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODC', null);

        $orden->orderType = 'CCom-'.$orden->orderType;

        if(!is_null($montoComision)) {
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
        $odc = $odc[0];

        $info = array();

        $getCurrUser     = new IntegradoSimple($this->integradoId);
        $titleArray      = array( $odc->numOrden );

        $array           = array(
            $getCurrUser->getDisplayName(),
            $odc->numOrden,
            JFactory::getUser()->name,
            date('d-m-Y'),
            '$'.number_format($odc->totalAmount, 2),
            strtoupper($odc->receptor->getDisplayName()) );

        $send            = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]            = $send->sendNotifications('14', $array, $titleArray);

        /*
         * Notificaciones 15&34
         */

        $titleArrayAdmin = array( $getCurrUser->getDisplayName(), $odc->numOrden );

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('15', $array, $titleArrayAdmin);

        return $info;
    }

    private function createOpossingOdv(OdCompra $odCompra){
        if($odCompra->getReceptor()->isIntegrado()){
            $catalogos  = new Catalogos();
            $save       = new sendToTimOne();
            $db         = JFactory::getDbo();
            $xml        = new xml2Array();
            $dataXML    = $xml->manejaXML(file_get_contents($odCompra->urlXML));

            $odv = new OdVenta();
            $odv->integradoId   = $odCompra->getEmisor()->id;
            $odv->numOrden      = $save->getNextOrderNumber('odv', $odCompra->getReceptor()->id);
            $odv->projectId     = $odCompra->getProjectName();
            $odv->projectId2    = $odCompra->getSubProjectName();
            $odv->clientId      = $odCompra->getReceptor()->id;
            $odv->account       = $odCompra->dataBank[0]->datosBan_id;
            $odv->paymentMethod = $odCompra->paymentMethod->id;
            $odv->conditions    = 2;
            $odv->placeIssue    = $catalogos->getStateIdByName($dataXML->emisor['children'][1]['attrs']['ESTADO']);
            $odv->setStatus(3);

            foreach ($dataXML->conceptos as $concepto) {
                foreach ($concepto as $key => $value) {
                    switch($key){
                        case 'DESCRIPCION':
                            $detalle['descripcion'] = $value;
                            break;
                        case 'UNIDAD':
                            $detalle['unidad'] = $value;
                            break;
                        case 'NOIDENTIFICACION':
                            $detalle['producto'] = $value;
                            break;
                        case 'CANTIDAD':
                            $detalle['cantidad'] = $value;
                            break;
                        case 'VALORUNITARIO':
                            $detalle['p_unitario'] = $value;
                            break;
                    }
                    $detalle['iva']  = $dataXML->impuestos->iva->tasa == 16 ? 3:0;
                    $detalle['ieps'] = isset($dataXML->impuestos->ieps->tasa) ? $dataXML->impuestos->ieps->tasa : 0;
                }
                $productos[] = $detalle;
            }

            $odv->setProductos(json_encode($productos));
            $odv->setCreatedDate(time());
            $odv->setTotalAmount(null);
            $odv->paymentDate = null;
            $odv->urlXML      = $odCompra->urlXML;

            $db->insertObject('#__ordenes_venta', $odv);

            $relation = new stdClass();
            $relation->id_odc = $odCompra->getId();
            $relation->id_odv = $db->insertid();

            $db->insertObject('#__ordenes_odv_odc_relation', $relation);

            $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->parametros) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');
        }
    }
}
