<?php
defined('_JEXEC');// or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');

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
        $data = JFactory::getApplication()->input->getArray();

        $objeto                  = new stdClass();
        $objeto->uuid            = $data['uuid'];
        $objeto->reference       = $data['reference'];
        $objeto->amount          = $data['amount'];
        $objeto->timestamp       = $data['timestamp'];
        $formato                 = 'json';
        $getMethod               = 'hello';
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
            7 => array('HTTP Response' => 405, 'Message' => 'No se guardo en Base de datos'),
            8 => array('HTTP Response' => 406, 'Message' => 'Integrado desconocido'),
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
            } else {
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
    public function processData($json){
        $file           = fopen("logs/requestFromTimone2.txt", "w+");
        $save           = new sendToTimOne();
        $post           = $json;
        $post->fecha    = date('d-m-Y', ($post->timestamp/1000) );
        $data_integrado = getFromTimOne::getIntegradoId($post->uuid);

        fwrite($file,
            'Los Datos que llegaron:'.PHP_EOL.
            'Uuid timone: '.$post->uuid.PHP_EOL.
            'Referencia: '.$post->reference.PHP_EOL.
            'Monto: '.$post->amount.PHP_EOL.
            'Fecha: '.$post->fecha.PHP_EOL.
            PHP_EOL
        );

        if ( ! empty( $data_integrado ) ) {
            fwrite($file,PHP_EOL.PHP_EOL.
                'Integrado id: '.$data_integrado[0]->integradoId.PHP_EOL.
                'CLABE: '.$data_integrado[0]->stpClabe.PHP_EOL.
                'Timone UUID: '.$data_integrado[0]->timoneUuid.PHP_EOL
            );

        } else {
            fwrite($file,PHP_EOL.PHP_EOL.'No encontro el integrado id: '.$data_integrado[0]->integradoId.PHP_EOL);
            // error no existe el integrado
            return array('code' => 0);
        }

        //TODO se deben traer solamente las ordenes no pagadas
        $odds        = getFromTimOne::getOrdenesDeposito($data_integrado[0]->integradoId);
        $post->fecha = date('d-m-Y', ($post->timestamp/1000) );

        foreach ($odds as $value) {
            $value->paymentDate = date('d-m-Y', ($value->paymentDate) );

            fwrite($file,
                PHP_EOL.
                'Inicia el foreach'.PHP_EOL.
                'Fecha de pago de la orden: '.$value->paymentDate.PHP_EOL.
                'Monto de la orden :'.$value->totalAmount.PHP_EOL.
                'Estatus de la orden: '.$value->status->id
            );

            if( ($post->fecha === $value->paymentDate) && ($post->amount == $value->totalAmount) && ($value->status->id == 5) ){
                fwrite($file,
                    PHP_EOL.
                    'Se encontro una coicidencia con una orden'.PHP_EOL
                );

                $dataTXMandato = array(
                    'idTx'        => $post->reference,
                    'idIntegrado' => $value->integradoId,
                    'date'        => time(),
                    'idComision'  => 1
                );

                fwrite($file,
                    PHP_EOL.
                    'Los Datos Son:'.PHP_EOL.
                    'Id de la transaccion: '.$dataTXMandato['idTx'].PHP_EOL.
                    'integradoId: '.$dataTXMandato['idIntegrado'].PHP_EOL.
                    'Fecha: '.$dataTXMandato['date'].PHP_EOL.
                    'id Comision: '.$dataTXMandato['idComision'].PHP_EOL
                );

                $save->formatData($dataTXMandato);

                $salvado = $save->insertDB('txs_timone_mandato');

                if($salvado){
                    fwrite($file,PHP_EOL.'Si se Salvo'.PHP_EOL);

                    $cambioStatus = $save->changeOrderStatus($value->id,'odd',13);
                    $return = array('code' => 1);
                    break;
                }else{
                    fwrite($file,PHP_EOL.'No se Salvo'.PHP_EOL);

                    $return = array('code' => 7);
                    break;
                }

            }

            fwrite($file, PHP_EOL.'Termina el foreach'.PHP_EOL);
        }

        if( !isset($return) ){
            fwrite($file,PHP_EOL.'No se encontraron Coicidencia con ODD'.PHP_EOL);

            $return = array('code' => 0);
        }

        fwrite($file,PHP_EOL.
            'Codigo: '.$return['code']
        );

        fclose($file);

        return $return;
    }

    function deliver_response($format, $api_response){

        $http_response_code = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Not Data Saved'
        );

        header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);

        if( strcasecmp($format,'json') == 0 ){
            header('Content-Type: application/json; charset=utf-8');

            $json_response = json_encode($api_response);

            $archivo = fopen('logs/respuestadelserviciocashIn.txt', 'w+');
            fwrite($archivo, $json_response);
            fclose($archivo);

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
