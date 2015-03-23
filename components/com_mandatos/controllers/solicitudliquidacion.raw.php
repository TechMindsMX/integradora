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

	/**
	 * @throws Exception
	 */
	function saveform() {
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
        $diccionario    = array('integradoId'   => array('number' => true, 'maxlength' => '1'),
            'monto'         => array('float' => true, 'maxlength' => '15'),
            'saldo'         => array('float' => true, 'maxlength' => '15'));
        $valida = $validacion->procesamiento($data, $diccionario);
        $document->setMimeEncoding('application/json');

        foreach ($valida as $key => $value) {
            if(!is_bool($value)){
                echo json_encode($valida);
                return;
            }
        }

        $txLiquidacion = new TxLiquidacion();

		try {
			$txLiquidacion->saveNewTx($data['monto'], $this->integradoId);
		} catch (Exception $e) {
			$respuesta = false;

			echo json_encode($respuesta);
			exit;
		}

        $sesion = JFactory::getSession();
        $nuevoSaldo = $data['saldo'] - $data['monto'];
        $idTX = $sesion->get('idTx', 0,'solicitudliquidacion');

        $sesion->set('idTx',$idTX+1, 'solicitudliquidacion');
        $sesion->set('nuevoSaldo',$nuevoSaldo, 'solicitudliquidacion');

        $respuesta = array();
        $respuesta['nuevoSaldo']     = (FLOAT) $nuevoSaldo;
        $respuesta['nuevoSaldoText'] = number_format($nuevoSaldo,2);
        $respuesta['idTx']           = (INT) $idTX;
        $respuesta['success']        = true;

//        $this->sendEmail($respuesta, $data);

        echo json_encode($respuesta);
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
}
