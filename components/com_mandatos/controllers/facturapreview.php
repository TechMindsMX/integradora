<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 */
class MandatosControllerFacturapreview extends JControllerAdmin {

	function cancel() {
		$this->app 			= JFactory::getApplication();

		$session            = JFactory::getSession();
		$integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $integradoId);

		if($this->permisos['canAuth']) {
			// acciones cuando tiene permisos para autorizar
			$this->app->redirect('index.php?option=com_mandatos&view=facturalist' ,'aqui enviamos a timone la autorizacion y redireccion con mensaje');
		} else {
			// acciones cuando NO tiene permisos para autorizar
			$this->app->redirect(JRoute::_(''), JText::_(''), 'error');
		}
	}
}
