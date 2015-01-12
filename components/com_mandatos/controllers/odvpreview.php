<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');

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
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odv', '3');
	            if ($statusChange){
		            $this->app->enqueueMessage(JText::_('ORDER_STATUS_CHANGED'));

                    $newOrden = getFromTimOne::getOrdenesVenta(null, $this->parametros['idOrden']);
                    if ( $newOrden->status == 5 && !$newOrden->urlXml ) {
                        $factObj = $save->generaObjetoFactura( $newOrden );

                        if ( $factObj != false ) {
                            $xmlFactura = $save->generateFacturaFromTimone( $factObj );

                            $file = $save->saveXMLFile( $xmlFactura );

                            /*NOTIFICACIONES 7*/
                            $titulo = JText::_('TITULO_7');
                            $titulo = str_replace('$num_factura', '<strong style="color: #000000">'.$data['factura'].'</strong>',$titulo);

                            $contenido = JText::_('NOTIFICACIONES_7');

                            $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$data['nameIntegrado'].'</strong>',$contenido);
                            $contenido = str_replace('$usuario', '<strong style="color: #000000">$'.$data['corrUser'].'</strong>',$contenido);
                            $contenido = str_replace('$numfactura', '<strong style="color: #000000">$'.$data['factura'].'</strong>',$contenido);
                            $contenido = str_replace('$cliente', '<strong style="color: #000000">$'.$data['cliente'].'</strong>',$contenido);
                            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);
                            $contenido = str_replace('$monto', '<strong style="color: #000000">'.$data('monto').'</strong>',$contenido);
                            $contenido = str_replace('$odv', '<strong style="color: #000000">'.$data('odv').'</strong>',$contenido);


                            $integrado              = new IntegradoSimple($this->integradoId);

                            $data['corrUser']       = $this->currUser->name;
                            $data['titulo']         = $titulo;
                            $data['nameIntegrado']  = $integrado->getDisplayName();
                            $data['body']           = $contenido;

                            $send                   = new Send_email();
                            $info = $send->notification($data);


                        }

                        if ( isset( $file ) ) {
                            if ( $file != false ) {
                                $save->formatData(array('columnas' => 'urlXML', 'valores' => $file ));
                                $where = 'id = '.$newOrden->id;
                                $save->updateDB('ordenes_vaneta', null, $where);
                            }
                        }
                    }
	            }

                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED'));
            }else{
                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
	}

}
