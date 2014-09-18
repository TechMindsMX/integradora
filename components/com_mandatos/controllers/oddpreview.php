<?php
defined('_JEXEC') or die('Restricted access');

/**
 * metodo de envio a TimOne
 */
class MandatosControllerOdcpreview extends JControllerAdmin {
	
	function authorize() {
		$this->app 			= JFactory::getApplication();
		$this->paramertros	= $this->app->input->getArray();
		
		$this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
		
	}
}
