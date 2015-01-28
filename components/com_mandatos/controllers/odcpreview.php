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
class MandatosControllerOdcpreview extends JControllerAdmin {
	
	function authorize() {
        $this->returnUrl = 'index.php?option=com_mandatos&view=odclist';;

        $post               = array('idOrden' => 'INT');
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray($post);

        $this->integradoId = Integrado::getSessionIntegradoIdOrRedirectWtihError( JUri::getInstance() );

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canAuth']) {
            $getCurrUser         = new Integrado($this->integradoId);
            $integradoSimple     = new IntegradoSimple($this->integradoId);
            $integradoSimple->getTimOneData();

            $this->checkSaldoSuficienteOrRedirectWithError( $integradoSimple );

            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();
            unset($this->parametros['integradoId']);

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'], 'odc_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=odclist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $resultado = $save->insertDB('auth_odc');

            if($resultado) {

                $catalogoStatus = getFromTimOne::getOrderStatusCatalog();
                $newStatusId  = 5;
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odc', $newStatusId);
                if ($statusChange){
                    $this->app->enqueueMessage(JText::sprintf('ORDER_STATUS_CHANGED', $catalogoStatus[$newStatusId]->name));

                    if(isset($this->integradoId)){

                        $titulo     = JText::_('TITULO_13');
                        $titulo     = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$titulo);

                        $contenido = JText::_('NOTIFICACIONES_13');

                        $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$integradoSimple->user->username.'</strong>',$contenido);
                        $contenido = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$contenido);
                        $contenido = str_replace('$usuario', '<strong style="color: #000000">$'.$getCurrUser->user->username.'</strong>',$contenido);
                        $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);

                        $data['titulo']         = $titulo;
                        $data['body']           = $contenido;
                        $data['email']          = $getCurrUser->user->email;

                        $send                   = new Send_email();
                        $send->notification($data);


                        $integradoAdmin     = new IntegradoSimple(93);
                        $getCurrUser         = new Integrado($this->integradoId);

                        $titulo     = JText::_('TITULO_14');
                        $titulo     = str_replace('$integrado', '<strong style="color: #000000">'.$integradoAdmin->user->username.'</strong>',$titulo);
                        $titulo     = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$titulo);

                        $contenido = JText::_('NOTIFICACIONES_14');

                        $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$integradoAdmin->user->username.'</strong>',$contenido);
                        $contenido = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$contenido);
                        $contenido = str_replace('$usuario', '<strong style="color: #000000">$'.$getCurrUser->user->username.'</strong>',$contenido);
                        $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);

                        $data['titulo']         = $titulo;
                        $data['body']           = $contenido;
                        $data['email']          = $integradoAdmin->user->email;

                        $send                   = new Send_email();
                        $send->notification($data);

                    }

	            }
                //TODO Ingresar el llamado al servicio de cashout para efectuar los pagos


                $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_AUTHORIZED'));
            }else{
                $this->app->redirect($this->returnUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($this->returnUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
	}

    private function checkSaldoSuficienteOrRedirectWithError( $integradoSimple ) {

        if ( $integradoSimple->timoneData->balance < $this->totalOperacion() ) {
            $this->app->redirect($this->returnUrl, 'ERROR_SALDO_INSUFICIENTE', 'error');
        }
    }

    /**
     * @return array
     */
    private function totalOperacion() {
        $orden = getFromTimOne::getOrdenesCompra( null, $this->parametros['idOrden'] );
        $orden = $orden[0];

        $comisiones = getFromTimOne::getComisionesOfIntegrado( $this->integradoId );

        $montoComision = 0;
        if ( isset( $comisiones ) ) {
            $montoComision = getFromTimOne::calculaComision( $orden, 'FACTURA', $comisiones );
        }

        $totalOperacion = (float) $orden->totalAmount + (float) $montoComision;

        return $totalOperacion;
    }
}
