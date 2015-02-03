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

    function authorize() {
        $this->app 			= JFactory::getApplication();
        $this->parametros['idOrden']	= $this->app->input->get('idOrden', null, 'INT');
        $this->sendNotifications($this->parametros );

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

                    $this->sendNotifications($this->parametros );

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

    private function sendNotifications( $datos) {
        var_dump($datos);
        $data[0] = '<table>';
        $data[2] = '</table>';
        foreach ( $datos as $key => $value ) {
            $data[] = '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
        }

        $integrado = new IntegradoSimple($this->integradoId);
        $nombreIntegrado = $integrado->getDisplayName();

        $titulo = JText::_('TITULO_29');

        $contenido = JText::sprintf('NOTIFICACIONES_`29', $nombreIntegrado, JFactory::getUser()->username, implode($data) );

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $dato['email']          = JFactory::getUser()->email;
        $send                   = new Send_email();
        $info = $send->notification($dato);

        $this->logEvent($info, $dato);

        $integradoAdmin     = new IntegradoSimple(93);

        $titulo = JText::_('TITULO_30');

        $contenido = JText::sprintf('NOTIFICACIONES_30', $nombreIntegrado, implode($data));

        $datoAdmin['titulo']         = $titulo;
        $datoAdmin['body']           = $contenido;
        $datoAdmin['email']          = $integradoAdmin->user->email;
        $send                   = new Send_email();
        $infoAdmin = $send->notification($datoAdmin);

        $this->logEvent($infoAdmin, $dato);
    }

    private function logEvent( $info, $dato ) {
        $logdata = $logdata = implode( ', ', array (
            JFactory::getUser()->id,
            $this->integradoId,
            __METHOD__,
            json_encode( array ( $info, $dato  ) )
        ) );
        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

    }
}
