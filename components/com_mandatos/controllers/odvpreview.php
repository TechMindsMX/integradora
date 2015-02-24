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

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

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
                            $file = $save->saveXMLFile( $xmlFactura );
                            $info = $this->sendEmail($newOrden);
                        }

                        if ( isset( $file ) ) {
                            if ( $file != false ) {
                                $save->formatData(array('urlXML' => $file ));
                                $where = 'id = '.$newOrden->id;
                                $save->updateDB('ordenes_venta', null, $where);
                            }
                        }

                        //TODO tx con TIMone
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

}
