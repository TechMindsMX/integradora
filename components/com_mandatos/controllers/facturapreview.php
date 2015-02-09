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
		$this->integradoId = $integradoId;
		$this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $integradoId);

		if($this->permisos['canAuth']) {
			// acciones cuando tiene permisos para autorizar
			$this->sendEmail();


			$this->app->redirect('index.php?option=com_mandatos&view=facturalist' ,'aqui enviamos a timone la autorizacion y redireccion con mensaje');
		} else {
			// acciones cuando NO tiene permisos para autorizar
			$this->app->redirect(JRoute::_(''), JText::_(''), 'error');
		}
	}

	/**
	 * @param $dato
	 */
	private function sendEmail()
	{
		/*
		 * NOTIFICACIONES 23
		 */
		$getCurrUser 		= new IntegradoSimple($this->integradoId);
		$info 				= array();
		$facturaNum 		= $this->app->input->getArray();
		$facturas 			= getFromTimOne::getFacturasVenta($this->integradoId);

		foreach ($facturas as $key => $value) {

			if($value->id == $facturaNum['facturanum']){
				$dataFactura = $value;
			}
		}
		$arrayTitle 		= array($dataFactura->numOrden);
		$array				= array($getCurrUser->getUserPrincipal()->name, $dataFactura->numOrden, JFactory::getUser()->username, date('d-m-Y'), $dataFactura->totalAmount, $dataFactura->proveedor->tradeName, $dataFactura->createdDate, $dataFactura->numOrden);

		$send = new Send_email();
		$send->setIntegradoEmailsArray($getCurrUser);

		$info[] = $send->sendNotifications('23', $array, $arrayTitle);

		/*
		 * Notificaciones 24
		 */

		$arrayTitleAdmin 	= array($getCurrUser->getUserPrincipal()->name, $dataFactura->numOrden);

		$send = new Send_email();
		$send->setAdminEmails();
		$info[] 			= $send->sendNotifications('24', $array, $arrayTitleAdmin);
	}
}
