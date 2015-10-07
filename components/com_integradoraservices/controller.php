<?php
use Integralib\RelacionaTx;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('html2pdf.PdfsIntegradora');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();


class IntegradoraservicesController extends JControllerLegacy {
    //Revisa si el usaurio existe dado un correo electronico
    protected $integradoId;

    function __construct() {
        $this->sesion = JFactory::getSession();
        $this->integradoId = $this->sesion->get('integradoId', null, 'integrado');

        parent::__construct();

    }

    public function cashin() {
        $data              = JFactory::getApplication()->input->getArray();
        $objeto            = new stdClass();
        $objeto->uuid      = $data['uuid'];
        $objeto->uuidTx    = $data['uuidtx'];
        $objeto->reference = $data['reference'];
        $objeto->amount    = $data['amount'];
        $objeto->timestamp = $data['timestamp'];
        $formato           = 'json';
        $getMethod         = 'hello';

        $HTTPS_required          = false;
        $authentication_required = false;
        $api_response_code       = array(
            0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
            1 => array('HTTP Response' => 200, 'Message' => 'Success'),
            2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
            3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
            4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
            5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
            6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format'),
            7 => array('HTTP Response' => 405, 'Message' => 'No se pudo Almacenar'),
            8 => array('HTTP Response' => 406, 'Message' => 'No existe el usuario'),
            9 => array('HTTP Response' => 407, 'Message' => 'No hay Ordenes generadas.')
        );

        $response['code']   = 0;
        $response['status'] = 404;
        $response['data']   = NULL;

        if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
            $response['code']   = 2;
            $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
            $response['data']   = $api_response_code[ $response['code'] ]['Message'];

            $this->deliver_response($formato, $response);
        }

        if( $authentication_required ){
            if( empty($_POST['username']) || empty($_POST['password']) ){
                $response['code'] = 3;
                $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
                $response['data'] = $api_response_code[ $response['code'] ]['Message'];

                $this->deliver_response($formato, $response);
            }elseif( $_POST['username'] != 'foo' && $_POST['password'] != 'bar' ){
                $response['code'] = 4;
                $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
                $response['data'] = $api_response_code[ $response['code'] ]['Message'];

                $this->deliver_response($formato, $response);
            }

        }

        if( strcasecmp($getMethod,'hello') == 0){
            $response = $this->processData($objeto);

            if(isset($response['error'])) {
                $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
                $response['Message'] = $api_response_code[$response['code']]['Message'];
            }
            else {
                $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
                $response['Message'] = $api_response_code[$response['code']]['Message'];
            }
        }

        $this->deliver_response($formato, $response);

        exit;
    }

    /**
     * Deliver HTTP Response
     * @param string $format The desired HTTP response content type: [json, html, xml]
     * @param string $api_response The desired HTTP response data
     * @return array
     **/
    function processData($json){
        $db             = JFactory::getDbo();
        $save           = new sendToTimOne();
        $post           = $json;
        $data_integrado = getFromTimOne::getIntegradoId($post->uuid);
        $integrado = new IntegradoSimple($data_integrado[0]->integradoId);

        if ( empty( $data_integrado ) ) {
            // error no existe el integrado
            return array('code' => '8');
        }

        foreach ($integrado->getAccountData() as $banco) {
            if($banco->bankName == 'STP' && $banco->banco_clabe == $integrado->getTimoneAccount()){
                $idCuenta = $banco->datosBan_id;
            }
        }

        $db->transactionStart();

        try {
            $dataTX = new stdClass();
            $dataTX->cuenta      = $idCuenta;
            $dataTX->referencia  = $post->reference;
            $dataTX->date        = time();
            $dataTX->amount      = $post->amount;
            $dataTX->integradoId = $integrado->getId();
            $dataTX->identified  = 1;

            $db->insertObject('#__txs_banco_integrado',$dataTX);
            $id_tx_banco = $db->insertid();

            $asociacion = new RelacionaTx();
            $asociacion->asociacionTxs($post->reference,$id_tx_banco,$integrado->getId());

            $getPDF = new PdfsIntegradora();
            $json->currency = 'MXN';
            $getPDF->createPDF($json, 'cashin');

            $db->transactionCommit();

            $return = array('code' => '1');//succes
        }catch (Exception $e) {
            $db->transactionRollback();
            $return = array('code' => '7');//fail
        }

        if( !isset($return) ){
            $return = array('code' => '9');
        }

        return $return;
    }

    function deliver_response($format, $api_response){

        $http_response_code = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Not Data Saved',
            406 => 'User not exist',
            407 => 'Not Orders generated'
        );

        header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);

        if( strcasecmp($format,'json') == 0 ){
            header('Content-Type: application/json; charset=utf-8');

            $json_response = json_encode($api_response);

            echo $json_response;

        }elseif( strcasecmp($format,'xml') == 0 ){
            header('Content-Type: application/xml; charset=utf-8');

            $xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
                '<response>'."\n".
                "\t".'<code>'.$api_response['code'].'</code>'."\n".
                "\t".'<data>'.$api_response['data'].'</data>'."\n".
                '</response>';

            echo $xml_response;
        }else{
            header('Content-Type: text/html; charset=utf-8');

            echo $api_response['data'];

        }
        exit;
    }
}