<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 15-Dec-14
 * Time: 3:35 PM
 */

use Integralib\OdCompra;
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.notifications');

/**
 * metodo de envio a TimOne
 * @property mixed $vars
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 */
class MandatosControllerAsociatxmandato extends JControllerLegacy {

	public function save( ) {
		JSession::checkToken() or die( 'Invalid Token' );
		$post               = array ( 'idOrden' => 'INT', 'orderType' => 'STRING', 'idTx' => 'INT' );
		$this->app          = JFactory::getApplication();
		$this->vars         = $this->app->input->getArray( $post );

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$redirectUrl = 'index.php?option=com_mandatos&view=txsinmandatolist';

		$this->permisos     = MandatosHelper::checkPermisos( __CLASS__, $this->integradoId );
		if ( !$this->permisos['canAuth'] ) {
			// acciones cuando NO tiene permisos para autorizar
			$this->exitWithRedirect($redirectUrl,'LBL_DOES_NOT_HAVE_PERMISSIONS');
		}

		$model    = $this->getModel('txsinmandatoform');
		$tx       = $model->getItem($this->vars['idTx']);
		$this->tx = $tx[0];

		$unpaidOrders   = $model->getOrdersCxC($this->integradoId);

		if(isset($this->vars['idOrden']) && isset($this->vars['orderType'])) {
			$this->order = $model->getOrderByIdAndType($unpaidOrders, $this->vars['idOrden'], $this->vars['orderType']);

			if($this->vars['orderType'] == 'odv') {
				if (!$this->checkOppssingOdc()) {
					$this->exitWithRedirect($redirectUrl, 'ERR_419_MANDATOSCONTROLLERASOCIATXMANDATOODC', 'error');
				}
			}
		} else {
			$this->exitWithRedirect($redirectUrl, 'ERR_413_MANDATOSCONTROLLERASOCIATXMANDATO', 'error');
		}

		if (!$this->doValidations()){
			$this->exitWithRedirect($redirectUrl, 'ERR_414_MANDATOSCONTROLLERASOCIATXMANDATO', 'error');
		}

		if($this->saveRelations()) {

			$this->exitWithRedirect($redirectUrl, 'COM_MANDATOS_LBL_SUCCESS');
		} else {
			$this->exitWithRedirect($redirectUrl, 'ERR_415_MANDATOSCONTROLLERASOCIATXMANDATO', 'error');
		}
	}

	/**
	 * @return bool
     */
	private function doValidations() {
		$return = true;
		if ($this->order->balance <= 0 || $this->tx->balance <= 0 ) {
			$return = false;
		}

		return $return;
	}

	/**
	 * @param $url
	 * @param $msg
	 * @param string $msgType
     */
	public function exitWithRedirect($url, $msg, $msgType = 'message') {
		$this->app->redirect($url, JText::_($msg), $msgType);
	}

	/**
	 * @return bool
	 */
	public function saveRelations() {
		$objToInsert            = new stdClass();
		$objToInsert->id        = $this->tx->id;
		$objToInsert->amount    = $this->setAmountTxToAssign();
		$objToInsert->idOrden   = $this->order->id;
		$objToInsert->orderType = $this->order->orderType;

		$db = JFactory::getDbo();
		$db->transactionStart();

		try {
			$db->insertObject( '#__txs_mandatos', $objToInsert );

			if ( ($this->order->balance - $objToInsert->amount) == 0 ) {
				$status = new sendToTimOne;
				if($this->order->orderType == 'odv'){
					$odcid = OrdenFn::getRelatedOdcIdFromOdvId($this->order->id);
					if( !is_null($odcid) ) {
						$odc = new OdCompra(null, $odcid);
						$status->changeOrderStatus($odcid, $odc->getOrderType(), 13);
					}
				}

				if (!$status->changeOrderStatus($this->order->id, $this->order->orderType, 13) ) {
					throw new Exception('LBL_CHANGE_STATUS_FAILED');
				}
			}

			$db->transactionCommit();

			$result = true;

		}
		catch ( Exception $e ) {
			$db->transactionRollback();

			$result = false;
		}
		if($result==true){
			$this->sendmail();
		}
		return $result;
	}

	/**
	 * @return mixed
     */
	private function setAmountTxToAssign() {
		return $this->tx->balance > $this->order->balance ? $this->order->balance : $this->tx->balance;
	}

	/**
	 *
     */
	private function sendmail() {

		/*
         * NOTIFICACIONES 35
         */

		$info           = array();
		if($this->order->paymentMethod->id==1){
			$metodoPago = JText::_('LBL_SPEI');
		}
		if($this->order->paymentMethod->id==2) {
			$metodoPago = JText::_('LBL_DEPOSIT');
		}
		if($this->order->paymentMethod->id==3) {
			$metodoPago = JText::_('LBL_CHEQUE');
		}

		$getCurrUser         = new IntegradoSimple($this->integradoId);

		$titleArray          = array($this->orden->numOrden);

		$array           = array(
			$getCurrUser->getDisplayName(),
			$this->order->numOrden,
			date('d-m-Y'),
			'$'.number_format($this->order->totalAmount, 2),
			$metodoPago);

		$send                = new Send_email();
		$send->setIntegradoEmailsArray($getCurrUser);
		$info[]              = $send->sendNotifications('35', $array, $titleArray);

		/*
         * NOTIFICACIONES 32
         */

		$titleArrayAdmin     = array($getCurrUser->getDisplayName(), $this->orden->numOrden);

		$send->setAdminEmails();
		$info[]             = $send->sendNotifications('36', $array, $titleArrayAdmin);
	}

	/**
	 * @return bool
	 */
	private function checkOppssingOdc()
	{
		$respuesta = false;

		$odcid = OrdenFn::getRelatedOdcIdFromOdvId($this->order->id);

		if( !is_null($odcid) ) {
			$odc = new OdCompra(null, $odcid);
			$respuesta = $odc->isAuthorized();
		}else{
			$respuesta = true;
		}

		return $respuesta;
	}

}
