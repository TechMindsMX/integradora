<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 */
class MandatosControllerOdvpreview extends JControllerLegacy {

	function authorize() {
		$this->app 			= JFactory::getApplication();
		$this->parametros	= $this->app->input->getArray();

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
