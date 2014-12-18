<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.gettimone');
jimport('integradora.webservice');

class MandatosModelMutuoslist extends JModelItem {
    public function __construct(){
        $app 				= JFactory::getApplication();
        $post               = array('integradoId' => 'INT', 'layout' => 'string');
        $this->catalogos    = $this->get('catalogos');
        $this->data			= (object) $app->input->getArray($post);

        parent::__construct();
    }

    public function getPost(){
        return $this->data;
    }

    public function getTiposPago(){
        $tipos = getFromTimOne::getTiposPago();

        return $tipos;
    }

    public function getCatalogos() {
        $catalogos = new Catalogos;

        $catalogos->getBancos();

        return $catalogos;
    }

    public function getMutuosAcreedor(){
        $allMutuos = getFromTimOne::getParametrosMutuo();
        $mutuosAcredor = array();

        foreach ($allMutuos as $value) {
            if($this->data->integradoId == $value->integradoIdE){
                $mutuosAcredor[] = $value;
            }
        }
        $mutuosAcredor = self::formatData($mutuosAcredor);
        $mutuos = $mutuosAcredor;

        return $mutuos;
    }

    public function getMutuosdeudor(){
        $allMutuos = getFromTimOne::getParametrosMutuo();
        $mutuosDeudor = array();

        foreach ($allMutuos as $value) {
            if($this->data->integradoId == $value->integradoIdR){
                $mutuosDeudor[] = $value;
            }
        }
        $mutuosDeudor = self::formatData($mutuosDeudor);
        $mutuos = $mutuosDeudor;

        return $mutuos;
    }

    public static function formatData($AllData){
        $mutuos        = $AllData;
        $tiposPeriodos =  new Catalogos();
        $tipos = $tiposPeriodos->getTiposPeriodos();

        foreach ($mutuos as $key => $value) {

            $tipo = $tipos[$value->paymentPeriod];
            $value->tipoPeriodo = $tipo->nombre;
            $value->duracion    = $value->quantityPayments/$tipo->periodosAnio;

            $integradoAcredor   = new stdClass();
            $integradoDeudor    = new stdClass();

            $inAcredor = new IntegradoSimple($value->integradoIdE);
            $inDeudor  = new IntegradoSimple($value->integradoIdR);

            $inAcredor = $inAcredor->integrados[0];
            $inDeudor  = $inDeudor->integrados[0];

            if( is_null($inAcredor->datos_empresa) ){
                $datos_personales = $inAcredor->datos_personales;
                $integradoAcredor->nombre = is_null($datos_personales->nom_comercial)?$datos_personales->nombre_representante:$datos_personales->nom_comercial;
            }else{
                $integradoAcredor->nombre = $inAcredor->datos_empresa->razon_social;
            }
            $integradoAcredor->banco = $inAcredor->datos_bancarios;
            $value->integradoAcredor = $integradoAcredor;

            if( is_null($inDeudor->datos_empresa) ){
                $datos_personalesD = $inDeudor->datos_personales;
                $integradoDeudor->nombre = is_null($datos_personalesD->nom_comercial)?$datos_personalesD->nombre_representante:$datos_personalesD->nom_comercial;
            }else{
                $integradoDeudor->nombre = $inDeudor->datos_empresa->razon_social;
            }
            $integradoDeudor->banco  = $inDeudor->datos_bancarios;
            $value->integradoDeudor  = $integradoDeudor;
        }

        return $mutuos;
    }

    public function getServicio(){
        $json                       = $_GET['tx'];
        $json                       = json_decode($json);
        $formato                    = isset($_GET['formatoResponse']) ? $_GET['formatoResponse'] : 'json';
        $getMethod                  = isset($_GET['metodo']) ? $_GET['formato'] : 'hello';
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
            $respuesta = $this->processData($json);

            if($respuesta) {
                $response['code'] = 1;
                $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
                $response['Message'] = $api_response_code[$response['code']]['Message'];
                $response['data'] = 'Hello World';
            }
        }

        deliver_response($formato, $response);
    }

    public function processData($json){
        $save           = new sendToTimOne();
        $post           = $json;//json_decode('{"idTx":1,"date":1418860800,"totalAmount":15000,"timOneId":1}');
        $data_integrado = getFromTimOne::getIntegradoId($post->timOneId);
        $data_integrado = $data_integrado[0];
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
}