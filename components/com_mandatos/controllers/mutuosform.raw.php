<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';
//TODO crear metodo de validación de campos para el formulario
class MandatosControllerMutuosform extends JControllerLegacy {

    public function __construct(){
        $this->document     = JFactory::getDocument();
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post               = array(
            'integradoId'       => 'INT',
            'id'                => 'INT',
            'integradoIdR'      => 'INT',
            'paymentPeriod'     => 'INT',
            'idCuenta'          => 'INT',
            'cuotaOcapital'     => 'INT',
            'quantityPayments'  => 'FLOAT',
            'totalAmount'       => 'FLOAT',
            'interes'           => 'FLOAT',
            'beneficiario'      => 'STRING',
            'jsonTabla'         => 'STRING',
            'rfc'               => 'STRING',
            'layout'            => 'STRING',
            'banco_codigo'      => 'STRING',
            'banco_cuenta'      => 'STRING',
            'banco_sucursal'    => 'STRING',
            'banco_clabe'       => 'STRING',
            'existe'            => 'STRING'
        );

        $this->parametros   = (object) $this->inputVars->getArray($post);

        parent::__construct();
    }

    function saveMutuo() {
        $this->document->setMimeEncoding('application/json');
        $db    = JFactory::getDbo();
        $datos = $this->parametros;
        $save  = new sendToTimOne();

        if($datos->integradoIdR == 0){
            $id = getFromTimOne::newintegradoId(0);
            $data_integrado = array(
                'nombre_representante' => $datos->beneficiario,
                'rfc'                  => $datos->rfc,
                'integrado_id'         => $id
            );

            $save->formatData($data_integrado);
            $creado = $save->insertDB('integrado_datos_personales');

            $dataBancaria = array(
                'banco_codigo'      => $datos->banco_codigo,
                'banco_cuenta'      => $datos->banco_cuenta,
                'banco_sucursal'    => $datos->banco_sucursal,
                'banco_clabe'       => $datos->banco_clabe,
                'integrado_id'       => $id
            );

            $save->formatData($dataBancaria);
            $datos->idCuenta = $save->insertDB('integrado_datos_bancarios',null,null,true);

        }else{
            $id = $datos->integradoIdR;
        }

        $dataMutuo = array(
            'integradoIdE'      => $datos->integradoId,
            'integradoIdR'      => $id,
            'idCuenta'          => $datos->idCuenta,
            'paymentPeriod'     => $datos->paymentPeriod,
            'quantityPayments'  => $datos->quantityPayments,
            'jsonTabla'         => $datos->jsonTabla,
            'totalAmount'       => $datos->totalAmount,
            'interes'           => $datos->interes,
            'cuotaOcapital'     => $datos->cuotaOcapital,
            'status'            => 1
        );

        $save->formatData($dataMutuo);
        if($datos->id == 0) {
            $id_mutuo = $save->insertDB('mandatos_mutuos', null, null, true);
        }else{
            $id_mutuo = $datos->id;
            $update = $save->updateDB('mandatos_mutuos', null,'id = '.$datos->id);
        }

        if($id_mutuo !== false){
            $respuesta['success'] = true;
            $respuesta['redirect'] = true;
            $respuesta['urlRedirect'] = 'index.php?option=com_mandatos&view=mutuospreview&integradoId='.$datos->integradoId.'&idMutuo='.$id_mutuo;
        }

        echo json_encode($respuesta);
    }

    function valida(){
        $validacion = new validador();
        $parametros = $this->parametros;
        $this->document->setMimeEncoding();

        if($parametros->existe === 'false') {
            $diccionario = array(
                'rfc'               => array('alphaNumber'  => true, 'maxlength' => '100',  'required' => true),
                'integradoIdE'      => array('string'       => true, 'maxlength' => '100'),
                'integradoIdR'      => array('string'       => true, 'maxlength' => '100'),
                'beneficiario'      => array('alphaNumber'  => true, 'maxlength' => '100',  'required' => true),
                'expirationDate'    => array('date'         => true, 'maxlength' => '10'),
                'payments'          => array('string'       => true, 'maxlength' => '10'),
                'totalAmount'       => array('float'        => true, 'maxlength' => '100'),
                'interes'           => array('float'        => true, 'maxlength' => '100',  'required' => true),
                'banco_codigo'      => array('alphaNumber'  => true, 'length'    => 3,      'required' => true),
                'banco_cuenta'      => array('required'     => true),
                'banco_sucursal'    => array('required'     => true),
                'banco_clabe'       => array('banco_clabe'  => $parametros->banco_codigo, 'length' => 18, 'required' => true));
        }elseif($parametros->existe === 'true'){
            $diccionario = array(
                'integradoIdE'      => array('string' => true, 'maxlength' => '100'),
                'expirationDate'    => array('date'   => true, 'maxlength' => '10'),
                'payments'          => array('string' => true, 'maxlength' => '10'),
                'totalAmount'       => array('float'  => true, 'maxlength' => '100'),
                'interes'           => array('float'  => true, 'maxlength' => '100', 'required' => true));
        }

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        foreach ($respuesta as $campo) {
            if(is_array($campo)){
                $respuesta['success'] = false;
                $respuesta['redirect'] = true;
                $respuesta['urlRedirect'] = 'index.php?option=com_mandatos';

                echo json_encode($respuesta);
                exit;
            }
        }
        $respuesta['success'] = true;
        $respuesta['redirect'] = true;
        echo json_encode($respuesta);
    }
}
