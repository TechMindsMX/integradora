<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllersolicitudliquidacion extends JControllerAdmin {

    protected $integradoId;
	protected $tx;

	/**
	 * @throws Exception
	 */
	function validateform() {
        $document       = JFactory::getDocument();
        $this->app 	    = JFactory::getApplication();
        $parametros     = array(
            'saldo'       => 'FLOAT',
            'monto'       => 'FLOAT'
        );
        $data           = $this->app->input->getArray($parametros);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $validacion     = new validador();
        $diccionario    = array(
	        'integradoId'   => array('number' => true, 'maxlength' => '1'),
            'monto'         => array('float' => true, 'maxlength' => '15', 'min' => 0.01, 'max' => $this->getBalance(), 'required' => true),
            'saldo'         => array('float' => true, 'maxlength' => '15')
        );
        $valida = $validacion->procesamiento($data, $diccionario);

        $document->setMimeEncoding('application/json');

        if (!$validacion->allPassed()) {

            echo json_encode($valida);
            return;
        } else {

	        $sesion = JFactory::getSession();

	        $idTX = $sesion->get('idTx', 0,'solicitudliquidacion');
	        $sesion->set('idTx',$idTX+1, 'solicitudliquidacion');
	        $sesion->set('amount',$data['monto'], 'solicitudliquidacion');

	        $respuesta['success'] = true;

	        echo json_encode($respuesta);
	        return;
        }

	}

    /**
     * @param $respuesta
     * @param $data
     */
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
