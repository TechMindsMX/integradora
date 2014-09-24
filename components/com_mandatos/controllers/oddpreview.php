<?php
defined('_JEXEC') or die('Restricted access');

/**
 * metodo de envio a TimOne
 */
class MandatosControllerOdcpreview extends JControllerAdmin {
	
	function authorize() {
        $this->app 			= JFactory::getApplication();
        $this->paramertros	= $this->app->input->getArray();

        $this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->parametros['integradoId']);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
        }
	}
}
