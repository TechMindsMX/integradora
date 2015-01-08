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
class MandatosControllerOddpreview extends JControllerAdmin {

    private $integradoId;

    function authorize() {
        $this->app 			= JFactory::getApplication();
        $this->parametros['idOrden']	= $this->app->input->get('idOrden', null, 'INT');

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
	            $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'odd', '5');
	            if ($statusChange){
		            $this->app->enqueueMessage(JText::_('ORDER_STATUS_CHANGED'));
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
}
