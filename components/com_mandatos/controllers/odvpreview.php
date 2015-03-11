<?php
use Integralib\OrderFactory;

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
class MandatosControllerOdvpreview extends JControllerLegacy {

	function authorize() {
        $post               = array('idOrden' => 'INT');
		$this->app 			= JFactory::getApplication();
		$this->parametros	= $this->app->input->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        $redirectUrl = 'index.php?option=com_mandatos&view=odvlist';

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']         = (INT)$user->id;
            $this->parametros['integradoId']    = $this->integradoId;
            $this->parametros['authDate']       = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odv_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $resultado = $save->insertDB('auth_odv');

            if($resultado) {
                // autorización guardada
                $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                $newStatusId  = 5;
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odv', $newStatusId);
                if ($statusChange){
                    $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));

                    $newOrden = getFromTimOne::getOrdenesVenta(null, $this->parametros['idOrden']);
                    $newOrden = $newOrden[0];
                    if ( $newOrden->status->id == 5 && is_null($newOrden->urlXML) ) {
                        $factObj = $save->generaObjetoFactura( $newOrden );

                        if ( $factObj != false ) {
                            $xmlFactura = $save->generateFacturaFromTimone( $factObj );
	                        try {
		                        $newOrden->urlXML = $save->saveXMLFile( $xmlFactura );
	                        } catch (Exception $e) {
		                        $msg = $e->getMessage();
		                        JLog::add($msg, JLog::ERROR, 'error');
		                        $this->app->enqueueMessage($msg, 'error');
	                        }
                            $info = $this->sendEmail($newOrden);
                        }

                        if ( isset( $newOrden->urlXML ) ) {
                            if ( $newOrden->urlXML != false ) {
                                $save->formatData(array('urlXML' => $newOrden->urlXML ));
                                $where = 'id = '.$newOrden->id;
                                $save->updateDB('ordenes_venta', null, $where);

								$this->createOpposingODC($newOrden);
                            }
                        }
                    }
	            }

                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED'));
            }else{
                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            //acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
	}

    /**
     * @param $factObj
     * @return array
     */
    public function sendEmail($factObj)
    {
        /*
         *  NOTIFICACIONES 7
         */
        $info = array();

        $getCurrUser     = new IntegradoSimple($this->integradoId);
        $titleArray      = array( $factObj->numOrden);

        $array           = array($getCurrUser->user->name, $factObj->numOrden, JFactory::getUser()->username, date('d-m-Y'), $factObj->totalAmount, $factObj->integradoName,  $factObj->numOrden);
        $send            = new Send_email();

        $send->setIntegradoEmailsArray($getCurrUser);
        $info[]            = $send->sendNotifications('7', $array, $titleArray);

        /*
         * Notificaciones 8
         */

        $titleArrayAdmin = array( $getCurrUser->user->username, $factObj->numOrden );
        $arrayAdmin      = array( $getCurrUser->user->username, $factObj->numOrden, JFactory::getUser()->username, date('d-m-Y'), $factObj->totalAmount, $factObj->integradoName,  $factObj->numOrden );

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('8', $arrayAdmin, $titleArrayAdmin);

        return $info;
    }

    public function getTotalAmount($productos){
        $totalAmount = 0;

        foreach ($productos as $producto) {
            if($producto->iva == 1){
                $producto->iva = 0;
            }
            if($producto->iva == 2){
                $producto->iva =11;
            }
            if($producto->iva == 3){
                $producto->iva = 16;
            }

            $total = ($producto->cantidad*$producto->p_unitario);
            $montoIva = $total*($producto->iva/100);
            $montoIeps = $total*($producto->ieps/100);

            $totalAmount = $total+$montoIva+$montoIeps+$totalAmount;
        }

        return $totalAmount;
    }

	/**
	 * @param $odv
	 *  Create ODC from ODV in case bith parties are Integrado
	 * @return bool
	 */
	private function createOpposingODC($odv) {

		$odvObj = new \Integralib\OdVenta();
		$odvObj->setOrderFromId($odv->id);

		if($odvObj->getReceptor()->isIntegrado()) {

			$save   = new sendToTimOne();
			$db     = JFactory::getDbo();

			$datos['integradoId'] = $this->integradoId;

			$odc = new OrdenFn();
			$odc->createdDate   = time();
			$odc->integradoId   = $odc->getIdReceptor($odv, 'odv');
			$odc->numOrden      = $save->getNextOrderNumber('odc', $odc->integradoId);
			$odc->status        = 1;
			$odc->proyecto      = $odv->projectId2 !== "0" ? $odv->projectId2 : $odv->projectId;
			$odc->proveedor     = $odc->getIdEmisor($odv, 'odv');
			$odc->paymentDate   = $odv->paymentDate;
			$odc->paymentMethod = $odv->paymentMethod->id;
			$odc->totalAmount   = $odvObj->getTotalAmount();
			$odc->urlXML        = $odv->urlXML;
			$odc->observaciones = '';
			$odc->bankId        = $odv->account;

			$db->transactionStart();

			try {
				$db->insertObject('#__ordenes_compra', $odc);

				$relation = new stdClass();
				$relation->id_odv = $odv->id;
				$relation->id_odc = $db->insertid();

				$db->insertObject('#__ordenes_odv_odc_relation', $relation);

				$db->transactionCommit();
			} catch (Exception $e) {
				$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($e, $this->parametros) ) ) );
				JLog::add($logdata, JLog::DEBUG, 'bitacora');

				$this->app->enquemessage('LBL_ERROR_CREATING_ODC');
			}
		}

		return isset($relation->id_odc);
	}

	public function sendEmailODC($odcObj)
	{
		/*
		 *  NOTIFICACIONES
		 */
		$info = array();

		$getCurrUser     = new IntegradoSimple($odcObj->integradoId);
		$titleArray      = array( $odcObj->numOrden);

		$array           = array($getCurrUser->user->name, $odcObj->numOrden, JFactory::getUser()->username, date('d-m-Y'), $odcObj->totalAmount, $odcObj->integradoName,  $odcObj->numOrden);
		$send            = new Send_email();

		$send->setIntegradoEmailsArray($getCurrUser);
		$info[]            = $send->sendNotifications('7', $array, $titleArray);

		/*
		 * Notificaciones
		 */

		$titleArrayAdmin = array( $getCurrUser->user->username, $odcObj->numOrden );
		$arrayAdmin      = array( $getCurrUser->user->username, $odcObj->numOrden, JFactory::getUser()->username, date('d-m-Y'), $odcObj->totalAmount, $odcObj->integradoName,  $odcObj->numOrden );

		$send->setAdminEmails();
		$info[] = $send->sendNotifications('8', $arrayAdmin, $titleArrayAdmin);

		return $info;
	}
}
