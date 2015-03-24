<?php
use Integralib\TxLiquidacion;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllersolicitudliquidacion extends JControllerAdmin {

    protected $integradoId;
	protected $tx;

	public function saveform() {
		$sesion = JFactory::getSession();
		$txAmount = $sesion->get('amount', null, 'solicitudliquidacion');
		$this->integradoId = $sesion->get('integradoId', null, 'integrado');

		$app = JFactory::getApplication();

		$txLiquidacion = new TxLiquidacion();

		try {
			$txLiquidacion->saveNewTx($txAmount, $this->integradoId);
		} catch (Exception $e) {
			$app->enqueueMessage(JText::_('ERR_LIQUIDACION_SALDO_FAILED', 'error'));
			$app->redirect('index.php?option=com_mandatos&view=solicitudliquidacion');

		}

		$nuevoSaldo = $this->getBalance();
		$sesion->set('nuevoSaldo',$nuevoSaldo, 'solicitudliquidacion');
		$idTx = $sesion->get('idTx', null, 'solicitudliquidacion');
		$sesion->clear('idTx', null, 'solicitudliquidacion');

		$respuesta = array();
		$respuesta['success']        = true;
		$respuesta['nuevoSaldo']     = (FLOAT) $nuevoSaldo;
		$respuesta['nuevoSaldoText'] = number_format($nuevoSaldo,2);
		$respuesta['idTx']           = (INT) $idTx;

//        $this->sendEmail($respuesta, $data);

		$app->enqueueMessage(JText::_('LBL_LIQUIDACION_SALDO_SUCCESSFUL'));
		$app->redirect('index.php?option=com_mandatos');

	}

    public function sendEmail($respuesta, $data)
    {
        $getIntegrado = new IntegradoSimple($this->integradoId);

        $array = array($getIntegrado->user->username, $respuesta['nuevoSaldo'], $getIntegrado->user->username, date('d-m-Y'));

        $send = new Send_email();
        $send->setIntegradoEmailsArray($getIntegrado);
        $send->sendNotifications('9', $array);
    }

	private function getBalance() {
		$model = $this->getModel('Solicitudliquidacion');

		return $model->balanceLiquidable();
	}
}
