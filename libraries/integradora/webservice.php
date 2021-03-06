<?php
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__).'/../..'));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/libraries/joomla/factory.php' );
require_once ( JPATH_BASE .'/configuration.php' );
require_once ('gettimone.php');
require_once ('integrado.php');

/**
 * Deliver HTTP Response
 * @param string $format The desired HTTP response content type: [json, html, xml]
 * @param string $api_response The desired HTTP response data
 * @return void
 **/
function processData($json){
    $save           = new sendToTimOne();
    $post           = $json;
    $data_integrado = getFromTimOne::getIntegradoId($post->timOneId);
    $data_integrado = $data_integrado[0];
    //TODO se deben traer solamente las ordenes no pagadas
    $odds           = getFromTimOne::getOrdenesDeposito($data_integrado->integradoId);

    getFromTimOne::convierteFechas($post);

    foreach ($odds as $value) {
        if( ($post->timestamps->date === $value->timestamps->paymentDate) && ($post->totalAmount == $value->totalAmount) ){
            $dataTXMandato = array(
                'idTx'        => $post->idTx,
                'idOrden'     => $value->id,
                'idIntegrado' => $value->integradoId,
                'date'        => time(),
                'tipoOrden'   => 'odd',
                'idComision'  => 1
            );
            $save->formatData($dataTXMandato);

            $salvado = $save->insertDB('txs_timone_mandato');

            if($salvado){
                //$cambioStatus = $save->changeOrderStatus($value->id,'odd',1);
                $return = true;
            }
        }
    }

    if( !isset($return) ){
        $return = false;
    }

    return $return;
}

function deliver_response($format, $api_response){

    $http_response_code = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found'
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
$json                       = $_GET['tx'];
$json                       = json_decode($json);
$formato                    = 'json';
$getMethod                  = 'hello';
$HTTPS_required             = false;
$authentication_required    = false;
$api_response_code          = array(
    0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
    1 => array('HTTP Response' => 200, 'Message' => 'Success'),
    2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
    3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
    4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
    5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
    6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);

$response['code']   = 0;
$response['status'] = 404;
$response['data']   = NULL;

if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
    $response['code']   = 2;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']   = $api_response_code[ $response['code'] ]['Message'];

    deliver_response($formato, $response);
}

if( $authentication_required ){
    if( empty($_POST['username']) || empty($_POST['password']) ){
        $response['code'] = 3;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data'] = $api_response_code[ $response['code'] ]['Message'];

        deliver_response($formato, $response);
    }elseif( $_POST['username'] != 'foo' && $_POST['password'] != 'bar' ){
        $response['code'] = 4;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data'] = $api_response_code[ $response['code'] ]['Message'];

        deliver_response($formato, $response);
    }

}

if( strcasecmp($getMethod,'hello') == 0){
    $respuesta = processData($json);

    if($respuesta) {
        $response['code'] = 1;
        $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
        $response['Message'] = $api_response_code[$response['code']]['Message'];
        $response['data'] = 'Hello World';
    }
}

deliver_response($formato, $response);