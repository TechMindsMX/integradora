<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.notifications');
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
			/*NOTIFICACIONES 21*/
			$integradoSimple     = new IntegradoSimple($this->integradoId);
			$getCurrUser         = new Integrado($this->integradoId);

			$titulo = JText::_('TITULO_21');

			$contenido = JText::_('NOTIFICACIONES_21');

			$dato['titulo']         = $titulo;
			$dato['body']           = $contenido;
			$dato['email']          = $getCurrUser->user->email;
			$send                   = new Send_email();
			$info = $send->notification($dato);

			$titulo = JText::_('TITULO_22');

			$contenido = JText::_('NOTIFICACIONES_22');

			$dato['titulo']         = $titulo;
			$dato['body']           = $contenido;
			$dato['email']          = $getCurrUser->user->email;
			$send                   = new Send_email();
			$info = $send->notification($dato);

			$this->app->redirect('index.php?option=com_mandatos&view=facturalist' ,'aqui enviamos a timone la autorizacion y redireccion con mensaje');
		} else {
			// acciones cuando NO tiene permisos para autorizar
			$this->app->redirect(JRoute::_(''), JText::_(''), 'error');
		}
	}
}
