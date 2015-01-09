<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 15-Dec-14
 * Time: 3:35 PM
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');

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
		$post               = array ( 'numOrden' => 'INT', 'orderType' => 'STRING', 'idTx' => 'INT' );
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


		$model          = $this->getModel('txsinmandatoform');
		$this->tx       = $model->getItem($this->vars['idTx']);

		$unpaidOrders = getFromTimOne::getOrdersCxP($this->integradoId);

		if(isset($this->vars['numOrden']) && isset($this->vars['orderType'])) {
			$this->order = $model->getOrderByIdAndType($unpaidOrders, $this->vars['numOrden'], $this->vars['orderType']);
		} else {
			$this->exitWithRedirect($redirectUrl, 'LBL_ERROR', 'error');
		}

		if (!$this->doValidations()){
			$this->exitWithRedirect($redirectUrl, 'LBL_ERROR', 'error');
		}

		$update = new sendToTimOne();

		$arrayToSave['idOrden'] = $this->order->id;
		$arrayToSave['tipoOrden'] = $this->order->orderType;
		$update->formatData($arrayToSave);

		// TODO: tabla pivot de asociación de tx y saldo parcial de una orden
		$where = 'id = '.$this->tx[0]->id;
		$result = $update->updateDB('txs_timone_mandato', null, $where);

		if($result) {
			$this->exitWithRedirect($redirectUrl, 'COM_MANDATOS_LBL_SUCCESS');
		} else {
			$this->exitWithRedirect($redirectUrl, 'LBL_ERROR', 'error');
		}
	}

	private function doValidations() {
		// TODO: Validar que pertenezcan al mismo integrado, que el monto ea suficiente y el estatus de la orden
		return true;
	}

	public function exitWithRedirect($url, $msg, $msgType = 'message') {
		$this->app->redirect($url, JText::_($msg), $msgType);
	}
}
