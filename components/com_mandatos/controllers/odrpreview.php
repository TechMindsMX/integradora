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
class MandatosControllerOdrpreview extends JControllerAdmin {

    function authorize() {
        $post               = array( 'idOrden' => 'INT' );
        $this->app 			= JFactory::getApplication();
        $this->parametros	= $this->app->input->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'odr_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $resultado = $save->insertDB('auth_odr');

            if($resultado) {
                // autorización guardada
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odr', '5');
                if ($statusChange){
                    $this->app->enqueueMessage(JText::_('ORDER_STATUS_CHANGED'));
                }

                $this->cashout();

                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_ORDER_AUTHORIZED'));
            }else{
                $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect('index.php?option=com_mandatos&view=odrlist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    private function cashout(){
        //cashOut si cambia al 5
        $orden = getFromTimOne::getOrdenesRetiro(null,$this->parametros['idOrden']);
        var_dump($orden);
        $orden = $orden[0];
        if($orden->status->id == 5){


            $data      = new Cashout($orden);

            $serviceUrl = 'http://192.168.0.111:8081/web/services/integra/stp/cashout';
            $jsonData   = json_encode( $data );
            $httpType = 'POST';

//  $ruta = $this->route->cashOutUrls();
//  var_dump($ruta);exit;
//  $httpType = $ruta->urls->create->type;


            $request = new sendToTimOne();
            $request->setServiceUrl( $serviceUrl );
            $request->setJsonData( $jsonData );
            $request->setHttpType( $httpType );

            $result = $request->to_timone(); // realiza el envio

            var_dump( $request );
            if ( isset( $result ) ) {
                var_dump( $result );
            }

            return $result;
        }
    }
}
