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

            $this->checkSaldoSuficienteOrRedirectWithError($integradoSimple);

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

		        $resultado = $save->insertDB( 'auth_odc' );

		        if ( $resultado ) {
			        $this->logEvents( __METHOD__, 'authorizacion_odc', json_encode( $save->set ) );

			        $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
			        $newStatusId    = 5;

			        $statusChange = $save->changeOrderStatus( $this->parametros['idOrden'], 'odc', $newStatusId );

			        if ( $statusChange ) {

				        $TxOdc = $this->realizaTx();

				        if ( $TxOdc ) {
					        $cobroComision = $this->txComision();

					        if ( $cobroComision ) {
						        $newStatusId  = 13;
						        $statusChange = $save->changeOrderStatus( $this->parametros['idOrden'], 'odc',
						                                                  $newStatusId );

						        if ( $statusChange ) {

							        $odc       = getFromTimOne::getOrdenesCompra( null, $this->parametros['idOrden'] );
							        $odc       = $odc[0];
							        $proveedor = new IntegradoSimple( $odc->proveedor->integrado->integrado_id );

							        if ( $proveedor->isIntegrado() ) { //operacion de transfer entre integrados
								        $odvId           = OrdenFn::getRelatedOdvIdFromOdcId( $odc->id );
								        $odvStatusChange = $save->changeOrderStatus( $odvId, 'odv', $newStatusId );
							        }

							        $this->app->enqueueMessage( JText::sprintf( 'ORDER_STATUS_CHANGED',
							                                                    $catalogoStatus[ $newStatusId ]->name ) );
						        }
					        } else {
						        $this->app->enqueueMessage( JText::sprintf( 'ORDER_PAID_AUTHORIZED',
						                                                    $catalogoStatus[ $newStatusId ]->name ) );
					        }
				        } else {

					        $statusChange = $save->changeOrderStatus( $this->parametros['idOrden'], 'odc', 3 );

					        if ( $statusChange ) {
						        $save->deleteDB( 'auth_odc', 'idOrden = ' . $this->parametros['idOrden'] );
						        $this->app->redirect( $this->returnUrl,
						                              JText::_( 'LBL_ORDER_NOT_AUTHORIZED', 'error' ) );
					        }
				        }
			        }

			        $this->sendEmail();

			        $db->transactionCommit();

			        $this->app->redirect( $this->returnUrl, JText::_( 'LBL_ORDER_AUTHORIZED' ) );
		        } else {
			        $this->app->redirect( $this->returnUrl, JText::_( 'LBL_ORDER_NOT_AUTHORIZED', 'error' ) );
		        }
	        } catch (Exception $e) {
		        $db->transactionRollback();

		        $msg = $e->getMessage();
		        JLog::add( $msg, JLog::ERROR, 'error' );
		        $this->app->enqueueMessage( $msg, 'error' );
		        $this->app->redirect( $this->returnUrl );
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

        return $txDone;
    }

    private function txComision(){
        //Metodo para realizar el cobro de comisiones Transfer de integrado a Integradora.
        $orden          = $this->getOrden();
        $montoComision  = getFromTimOne::calculaComision($orden, 'ODC', $this->comisiones);

        $orden->orderType = 'CCom-'.$orden->orderType;

        $txComision     = new transferFunds($orden,$orden->integradoId,1,$montoComision);

        return $txComision->sendCreateTx();
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
}
