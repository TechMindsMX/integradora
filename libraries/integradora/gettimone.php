<?php
use Integralib\TimOneRequest;

defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('joomla.log.log');
jimport('integradora.catalogos');
jimport('integradora.rutas');
jimport('integradora.xmlparser');
jimport('integradora.integrado');
jimport('integradora.mutuo');

class getFromTimOne{
    public static function getOrdenAuths($idOrden, $tipo){
        $tabla = sendToTimOne::getTableByType($tipo);

        $authorizations = self::selectDB($tabla,'idOrden = '.$idOrden);

        if (isset($authorizations)) {
            foreach($authorizations as $key => $value){
                $value->idOrden     = (INT)$value->idOrden;
                $value->userId      = (INT)$value->userId;
                $value->integradoId = (INT)$value->integradoId;
                $value->authDate    = (STRING)$value->authDate;
            }
        }

        return $authorizations;
    }

    public static function checkUserAuth($auths){
        $integradoId = JFactory::getSession()->get('integradoId', null, 'integrado');
        $userAsAuth = false;

        foreach ($auths as $auth) {
            if($auth->integradoId === (INT)$integradoId) {
                $userAsAuth = true;
            }
        }

        return $userAsAuth;
    }

    public static function getintegrados(){
        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query->select('*')
            ->from($db->quoteName('#__integrado_users'))
            ->where($db->quoteName('integrado_principal').' = 1');

        try {
            $db->setQuery($query);
            $results = $db->loadObjectList();
        }catch (Exception $e){
            $results = $e;
            exit;
        }

        foreach ($results as $value) {
            $integrado = new IntegradoSimple($value->integrado_id);
            $integrado->integrados[0]->displayName = $integrado->getDisplayName();
            $integradosArray[] = $integrado->integrados[0];
        }

        return $integradosArray;
    }

    public static function getDataFactura($orden) {
        $urlXML = $orden->urlXML;
        $xmlFileData  = file_get_contents(JPATH_SITE.DIRECTORY_SEPARATOR.$urlXML);
        $manejadorXML = new xml2Array();
        $datos 		  = $manejadorXML->manejaXML($xmlFileData);

        $orden->impuestos = $datos->impuestos->totalTrasladados;

        //tomo los productos de la factura
        foreach ($datos->conceptos as $value) {
            $orden->productos[] = $value;
        }

        foreach ($datos->impuestos as $key => $value) {
            if($key == 'iva'){
                $orden->iva = $value;
            }elseif($key == 'ieps'){
                $orden->ieps = $value;
            }
        }

        return $orden;
    }

    public static function getTxIntegradoSinMandato($integradoId=null, $idTX = null)
    {
        $where = null;
        if (!is_null($idTX)) {
            $where = 'id = ' . $idTX;
        } elseif (!is_null($integradoId)) {
            $where = 'idIntegrado = ' . $integradoId;
        }

        $txs = self::selectDB('txs_timone_mandato',$where);

        return $txs;
    }

    public static function getTxConciliacionesBanco($where){

        $txs = self::selectDB('txs_banco_integrado',$where);

        foreach ($txs as $value) {
            $value->id              = (INT) $value->id;
            $value->cuenta          = (INT) $value->cuenta;
            $value->date            = (INT) $value->date;
            $value->amount          = (FLOAT) $value->amount;
            $value->integradoId     = (INT) $value->integradoId;
            $value->fechaTimestamp  = $value->date;
            self::convierteFechas($value);
        }
        return $txs;
    }

    public static function getTablaAmotizacion($data){

        /*
         * object(stdClass)#354 (4) {
         *  ["tiempoplazo"]=>
         *  float()
         *  ["tipoPlazo"]=>
         *  float()
         *  ["capital"]=>
         *  float()
         *  ["interes"]=>
         *  float()
         * }
         * */
        $factorIva = CatalogoFactory::create()->getFullIva() / 100;

        $tabla= new stdClass();
        $tabla->intereses_con_iva = $data->interes * (1 + $factorIva );
        $tabla->capital           = $data->totalAmount;
        $tabla->tipoPeriodos      = $data->quantityPayments;
        switch($data->paymentPeriod){
            case 1:
                $tabla->tperiodo        = 'Diaria';
                $tabla->periodos_year   = '365';
                break;
            case 2:
                $tabla->tperiodo         = 'Quincenal';
                $tabla->periodos_year    = '24';
                break;
            case 3:
                $tabla->tperiodo         = 'Mensual';
                $tabla->periodos_year    = '12';
                break;
            case 4:
                $tabla->tperiodo         = 'Bimestral';
                $tabla->periodos_year    = '6';
                break;
            case 5:
                $tabla->tperiodo         = 'Trimestral';
                $tabla->periodos_year    = '4';
                break;
            case 6:
                $tabla->tperiodo         = 'Semestral';
                $tabla->periodos_year    = '2';
                break;
            case 7:
                $tabla->tperiodo         = 'Anual';
                $tabla->periodos_year    = '1';
                break;
            default:
                break;
        }

        $temp           = (float) $tabla->intereses_con_iva/100;
        $temp           = (float) $temp/$data->quantityPayments;
        $temp           = (float) $temp+1;
        $temp           = (float) pow($temp, $data->quantityPayments);
        $temp           = (float) $temp-1;
        $temp           = (float) $temp*$data->quantityPayments;

        $tabla->tasa_periodo           = (float) $tabla->intereses_con_iva;
        $tabla->tasa_efectiva_periodo  = (float) $temp*100;
        $tabla->capital_fija           = (float) $data->totalAmount/$data->quantityPayments;
        $final                         = (float) $tabla->capital;
        $capital                       = (float) $tabla->capital_fija;

        for($i = 1; $i <= $data->quantityPayments; $i++ ){
            $inicial              = (float)$final;
            $intiva               = (float)$inicial*($tabla->intereses_con_iva/100);

            $intereses            = (float)$intiva / (1 + $factorIva );
            $iva                  = (float)$intereses * $factorIva;
            $cuota                = (float)$tabla->capital_fija+$intiva;
            $final                = (float)$inicial-$tabla->capital_fija;

            $tabla->amortizacion_capital_fijo[]= array(
                'periodo'       => (float) $i,
                'inicial'       => (float) $inicial,
                'cuota'         => (float) $cuota,
                'intiva'        => (float) $intiva,
                'intereses'     => (float) $intereses,
                'iva'           => (float) $iva,
                'acapital'      => (float) $tabla->capital_fija,
                'final'         => (float) $final
            );
        }

        $temp                           = (float) 1+($tabla->intereses_con_iva/100);
        $temp                           = (float) pow($temp ,$data->quantityPayments);
        $number1                        = (float) $temp*($tabla->intereses_con_iva/100);
        $number2                        = (float) $temp-1;
        if ( $number2 != 0 ) {
            $tabla->factor                  = (float) $number1/$number2;
        } else {
            $tabla->factor = 1/$data->quantityPayments;
        }

        $tabla->cuota_Fija              = (float) $tabla->factor*$tabla->capital;
        $saldo_final                    = (float) $tabla->capital;

        for($i = 1; $i <= $data->quantityPayments; $i++ ){
            $saldo_inicial                    = (float)$saldo_final;
            $intiva                           = (float)$saldo_inicial*($tabla->intereses_con_iva/100);
            $intereses                        = (float)$intiva / (1 + $factorIva );
            $iva                              = (float)$intereses * $factorIva;
            $saldo_final                      = (float)$saldo_inicial-($tabla->cuota_Fija-$intiva);
            $tabla->amortizacion_cuota_fija[] = array(
                'periodo'       => (float)$i,
                'inicial'       => (float)$saldo_inicial,
                'cuota'         => (float)$tabla->cuota_Fija,
                'intiva'        => (float)$intiva,
                'intereses'     => (float)$intereses,
                'iva'           => (float)$iva,
                'acapital'      => (float)$tabla->cuota_Fija-$intiva,
                'final'         => (float)$saldo_final
            );


        }

        $tabla->intereses_con_iva = (float) $tabla->intereses_con_iva;
        $tabla->tasa_periodo      = (float) $tabla->tasa_periodo;

        return $tabla;
    }

    /**
     * Metodo que retorna
     * 1. todos los mutuos de un integrado
     * 2. un mutuo según su id
     * 3. todos los mutuos
     *
     * @param null $integradoId
     * @param null $idMutuo
     *
     * @return array
     */
    public static function getMutuos($integradoId=null, $idMutuo=null){
        $where = null;
        if(isset($idMutuo) && is_null($integradoId)){
            $where = 'id = '.$idMutuo;
        }elseif(isset($integradoId) && is_null($idMutuo)){
            $where = 'integradoIdE = '.$integradoId;
        }
        $mutuos = self::selectDB('mandatos_mutuos',$where,'');

        $dataFormater = new mutuo();

        $mutuos = $dataFormater->formatData($mutuos);

        return $mutuos;
    }

    /**
     * Metodo que retorna las ODP agrupadas por el usuario, cuando es acreedor y deudor
     * realiza una busqueda en la tabla ordenes_prestamo
    los parametros recibidos son:
    1.- Id integrado
    2.- Id mutuo
    Se recibe solamente uno de los dos.
    --Al recibir el Id integrado realiza la busqueda en la tabla y regresa
    un arreglo con dos nodos
    a) Acreedor: Este nodo tiene en su contenido todos las coincidencias encontradas para el Id integrado con un acreedor
    b) Deudor:   Este nodo muestra todas las coincidencias para un Id integrado y el deudor
    --Si recibe el Id mutuo realiza la busqueda por este id y regresa sus resultados encontrados

     * @param null $integradoId
     * @param null $idMutuo
     *
     * @return stdClass
     */
    public static function getMutuosODP($integradoId=null, $idMutuo=null){
        $where = null;
        $respuesta              = new stdClass();
        if(is_null($integradoId) && is_null($idMutuo)){
            $where = null;
        }elseif(!is_null($integradoId) && is_null($idMutuo)){

            $where      = 'acreedor = '.$integradoId;
            $acredor    = self::selectDB('ordenes_prestamo',$where);
            $where      = 'deudor='.$integradoId;
            $deudor     = self::selectDB('ordenes_prestamo',$where);
            $respuesta->acreedor    = $acredor;
            $respuesta->deudor      = $deudor;
        }elseif(!is_null($idMutuo) && is_null($integradoId)){
            $where = 'mutuo = '.$idMutuo;
            $mutuo      = self::selectDB('ordenes_prestamo',$where);
            $respuesta->mudutuo     = $mutuo;
        }

        return $respuesta;
    }

    public static function getTiposPago()
    {
        $tipo = array(
            2 => 'Quincenal',
            3 => 'Mensual',
            4 => 'Bimestral',
            5 => 'Trimestral',
            6 => 'Semestral',
            7 => 'Anual'
        );

        return $tipo;
    }

    public static function getIntegradoId($timOneId){
        $data = self::selectDB('integrado_timone','timoneUuid = '.$timOneId);

        return $data;
    }

    public static function filterByDate( $orders, $timestampStart = null, $timestampEnd = null) {
        $filteredOrders = array();

        $timestampEnd = isset($timestampEnd) ? $timestampEnd : time();

        if ( isset( $timestampStart ) ) {
            foreach ( $orders as $key => $val ) {
                if ($val->timestamps->paymentDate > $timestampStart && $val->timestamps->paymentDate < $timestampEnd) {
                    $filteredOrders[] = $val;
                }
            }
        } else {
            foreach ( $orders as $key => $val ) {
                if ( $val->timestamps->paymentDate < $timestampEnd ) {
                    $filteredOrders[] = $val;
                }
            }
        }
        return $filteredOrders;
    }

    public static function filterTxsByDate( $txs, $timestampStart = null, $timestampEnd = null) {

        $filteredTxs = array();

        $timestampEnd = isset($timestampEnd) ? $timestampEnd : time();

        if ( isset( $timestampStart ) ) {
            foreach ( $txs as $key => $val ) {

                if (!is_object($val->data->data) ) {
                    $val->data = json_decode($val->data->data);
                }

                $val->data->date = getFromTimOne::convertDateLength($val->data->timestamp, 10);
                if ($val->data->date > $timestampStart && $val->data->date < $timestampEnd) {
                    $filteredTxs[] = $val;
                }
            }
        } else {
            foreach ( $txs as $key => $val ) {
                if ( $val->data->date < $timestampEnd ) {
                    $filteredTxs[] = $val;
                }
            }
        }

        return $filteredTxs;
    }


    /**
     * @param $url
     *
     * @return string
     * @throws Exception
     */
    public static function generatePrintButton( $url ) {
// Vista previa de impresion
        $app      = JFactory::getApplication();
        $document = JFactory::getDocument();

        $isModal  = $app->input->get( 'print' ) == 1; // 'print=1' will only be present in the url of the modal window, not in the presentation of the page
        $template = $app->getTemplate();
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/bootstrap/output/bootstrap.css' );
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/bootstrap/output/bootstrap-responsive.css' );
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/bootstrap.css' );
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/template.css' );
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/override.css' );
        if ( $isModal ) {
            $href = '"#" onclick="window.print(); return false;"';
        } else {
            $href = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
            $href = "window.open(this.href,'win2','" . $href . "'); return false;";
            $href = $url . '&tmpl=component&print=1" onclick="' . $href;
        }

        return '<a class="btn btn-default" href="' . $href . '">' . JText::_( 'LBL_IMPRIMIR' ) . '</a>';
    }

    /**
     * @param $integradoId
     * @param $orderType
     * @param $idOrden
     *
     * @return array
     */
    public static function getTxbyOrderTypeAndOrderId($integradoId, $orderType, $idOrden){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('tm.idIntegrado, tm.idTx, tm.idComision, tm.date, ma.*')
            ->from($db->quoteName('#__txs_timone_mandato', 'tm'))
            ->join('LEFT', $db->quoteName('#__txs_mandatos', 'ma'). ' ON (tm.id = ma.id)' )
            ->where('ma.orderType = '.$db->quote($orderType). ' AND '. 'ma.idOrden = '.$db->quote($idOrden) );
        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }

    public static function sumaOrders($orders){
        $neto = 0;
        $iva = 0;
        $total = 0;

        $obj = new stdClass();
        $obj->pagado->total = array();
        $obj->pagado->iva = array();
        $obj->pagado->neto = array();

        foreach ( $orders as $order ) {
            $neto = $neto + $order->subTotalAmount;
            $iva = $iva + $order->iva;
            $total = $total + $order->totalAmount;

            $montoTxs = 0;
            foreach ($order->txs as $tx) {
                $montoTxs = $montoTxs + $tx->detalleTx->amount;
                $tx->detalleTx->ivaProporcion = $tx->detalleTx->amount * ($order->iva / $order->subTotalAmount);
            }
            //TODO verificar IVA de saldo
            $order->saldo->total = $order->totalAmount - $montoTxs;
            $order->saldo->iva   = $montoTxs * ($order->iva / $order->subTotalAmount);

            $obj->pagado->total[] = $montoTxs;
            $obj->pagado->iva[] = $order->saldo->iva;
            $obj->pagado->neto[] = $montoTxs - $order->saldo->iva;
        }

        $obj->pagado->total     = array_sum($obj->pagado->total);
        $obj->pagado->iva       = array_sum($obj->pagado->iva);
        $obj->pagado->neto      = array_sum($obj->pagado->neto);

        $obj->neto = $neto;
        $obj->iva = $iva;
        $obj->total = $total;

        return $obj;
    }

    private static function convertDateLength( $date, $int ) {

        $length = ceil(log10($date));
        if ($length > $int) {
            $array = str_split($date, $int);
            $date = (int)$array[0];
        }

        return $date;
    }

    private static function getNombreEstado( $code ) {

        $where = 'id = '.(int)$code;
        $nombreEstado = getFromTimOne::selectDB('catalog_estados', $where);

        $return = !empty($nombreEstado) ? $nombreEstado[0] : false;

        return $return;
    }

    public static function getOrdenesPrestamo($idMutuo=null,$idOrden=null){
        if( is_null($idOrden) ){
            $where = 'idMutuo = '.$idMutuo;
        }else{
            $where = 'id = '.$idOrden;
        }

        $ordenes = self::selectDB('ordenes_prestamo',$where);
        $odps    = array();

        foreach ($ordenes as $key => $value) {
            $orden = new stdClass();

            $orden->id                = (INT)$value->id;
            $orden->idMutuo           = (INT)$value->idMutuo;
            $orden->numOrden          = (STRING)$value->numOrden;
            $orden->fecha_elaboracion = (INT)$value->fecha_elaboracion;
            $orden->fecha_deposito    = (INT)$value->fecha_deposito;
            $orden->tasa              = (FLOAT)$value->tasa;
            $orden->tipo_movimiento   = (STRING)$value->tipo_movimiento;

            $orden->integradoIdA      = (INT)$value->integradoIdA;
            $integradoAcreedor        = new IntegradoSimple($orden->integradoIdA);
            $orden->acreedor          = (STRING)$value->acreedor;
            $orden->a_rfc             = (STRING)$value->a_rfc;
            $orden->acreedorDataBank  = $integradoAcreedor->integrados[0]->datos_bancarios[0];

            $orden->integradoIdD      = (INT)$value->integradoIdD;
            $integradoDeudor          = new IntegradoSimple($orden->integradoIdD);
            $orden->deudor            = (STRING)$value->deudor;
            $orden->d_rfc             = (STRING)$value->d_rfc;
            $orden->deudorDataBank    = $integradoDeudor->integrados[0]->datos_bancarios[0];

            $orden->capital           = (FLOAT)$value->capital;
            $orden->intereses         = (FLOAT)$value->intereses;
            $orden->iva_intereses     = (FLOAT)$value->iva_intereses;
            $orden->status            = (INT)$value->status;

            $orden->statusName = getFromTimOne::getOrderStatusName($orden->status);

            $odps[] = $orden;
        }

        return $odps;
    }

    public static function getTimoneUserDetalis($uuidTimone){
        $send  = new sendToTimOne();
        $rutas = new servicesRoute();
        $get = $rutas->getUrlService('timone', 'user', 'details');

        $serviceUrl = str_replace('{uuid}',$uuidTimone,$get->url);

        $send->setHttpType($get->type);
        $send->setServiceUrl($serviceUrl);
        $send->setJsonData('');

        $result = $send->to_timone();
        $datos = json_decode($result->data);

        return $datos;
    }

    public static function getClientProvider( $client_id ) {
        $client = array();

        $clientes = self::getClientes();

        foreach ( $clientes as $key => $value ) {
            if ( $client_id == $value->client_id ) {
                $client[$key] = $value;
            }
        }

        return $client;
    }

    public static function getClientProviderFromIntegradoId( $integrado_id ) {
        $client = array();

        $clientes = self::getClientes();

        foreach ( $clientes as $key => $value ) {
            if ( $integrado_id == $value->idCliPro ) {
                $client = $value;
            }
        }

        return $client;
    }

    public static function getBasicStatusCatalog() {
        $catalog = new Catalogos();

        return $catalog;
    }

    private static function getClientProviderName($clientProvider){
        $clientProvider->frontName = $clientProvider->corporateName == '' ? $clientProvider->tradeName : $clientProvider->corporateName;
    }

    public static function getBankName($arrayBancos){
        $catalogos = new Catalogos();
        $bancos    = $catalogos->getBancos();
        if ( ! empty( $bancos ) ) {
            foreach($arrayBancos as $bancoData){
                foreach ($bancos as $banco) {
                    if($banco->claveClabe == $bancoData->banco_codigo){
                        $bancoData->bankName = $banco->banco;
                    }
                }
            }
        } else {
            $logdata = implode(' | ',array(JFactory::getUser()->id, '', __METHOD__.':'.__LINE__, 'BANCOS VACIO' ) );
            JLog::add($logdata,JLog::ERROR,'Error SERVICIOS');
        }

        return $arrayBancos;
    }
    private static function getDataBankByBankId($bankId){
        $banco = self::selectDB('integrado_datos_bancarios', 'datosBan_id = '.$bankId);
        $banco = self::getBankName($banco);

        return $banco;
    }

    /**
     * @param $tipoOrden
     * @param $comisiones
     * @return mixed
     */
    public static function getAplicableComision($tipoOrden, $comisiones){
        $comision = null;

        switch ( strtoupper($tipoOrden) ) {
            case 'FACTURA':
                $triggerSearch = 'factpagada';
                break;
            case 'ODC':
                $triggerSearch = 'odcpagada';
                break;
            case 'ODD':
                $triggerSearch = 'oddpagada';
                break;
            case 'ODR':
                $triggerSearch = 'odrpagada';
                break;
        }

        if (!empty($comisiones) && isset($triggerSearch)) {
            foreach ($comisiones as $key => $com) {
                if ($com->trigger == $triggerSearch) {
                    $comision = $com;
                }
            }
        }

        return $comision;
    }

    public static function limpiarPostPrefix( $data, $prefijo, $columnasValoresArray ) {
        $db        = JFactory::getDbo();
        $columnas  = $columnasValoresArray['columnas'];
        $valores   = $columnasValoresArray['valores'];
        $setUpdate = $columnasValoresArray['setUpdate'];

        foreach ( $data as $key => $value ) {
            $columna = substr( $key, 3 );
            $clave   = substr( $key, 0, 3 );

            if ( $clave == $prefijo ) {
                $columnas[]  = $columna;
                $valores[]   = $db->quote( $value );
                $setUpdate[] = $db->quoteName( $columna ) . ' = ' . $db->quote( $value );
            }
        }

        $columnasValoresArray['columnas']  = $columnas;
        $columnasValoresArray['valores']   = $valores;
        $columnasValoresArray['setUpdate'] = $setUpdate;

        return $columnasValoresArray;
    }

    /**
     * @return string separado por comas
     */
    public static function acceptedCurrenciesList() {
        /** @var string $acceptedCurrenciesList se traerá como parametro en futura versión */
        $acceptedCurrenciesList = '"MXN"';

        return $acceptedCurrenciesList;
    }

    public static function searchBancoByClabe( $banco_clabe ) {
        $db = JFactory::getDbo();

        // busca los datos bancario por la CLABE
        $table = 'integrado_datos_bancarios';
        if ( empty( $banco_clabe ) ) {
            $banco_clabe = '0000000';
        }
        $where  = $db->quoteName( 'banco_clabe' ) . ' = ' . $banco_clabe;
        $existe = getFromTimOne::selectDB( $table, $where );

        return !empty($existe)?$existe[0]:null;
    }

    public static function getPersJuridica( $string ) {
        $cat = new Catalogos();
        $persJuridicas = array_flip( $cat->getPesonalidadesJuridicas() );

        return $persJuridicas[ucfirst($string)];
    }

    private static function getTxUUID( $txId ) {
        $result = self::selectDB('txs_timone_mandatos', 'id = '.$txId);

        return $result[0]->idTx;
    }

    public function createNewProject($envio, $integradoId){
        $jsonData = json_encode($envio);

        $route = new servicesRoute();
        $route->projectUrls()->urls;

        $serviceUrl = str_replace('{userId}', $integradoId, $route->baseUrl.$route->urls->create->url);

        $sendToTimone = new sendToTimOne();
        $sendToTimone->setHttpType($route->urls->create->type);
        $sendToTimone->setJsonData($jsonData);
        $sendToTimone->setServiceUrl($serviceUrl);

        $result = $sendToTimone->to_timone();

        return $result;
    }

    public static function selectDB($table, $where = null, $keyAssoc = '', $class = 'stdClass'){
        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);
        if(!is_null($where)){
            $query->select('*')
                ->from($db->quoteName('#__'.$table))
                ->where($where);
        }else{
            $query->select('*')
                ->from($db->quoteName('#__'.$table));
        }
        try {
            $db->setQuery($query);
            $results = $db->loadObjectList($keyAssoc, $class);
        }catch (Exception $e){
            echo '<pre>';
            var_dump($e);
            exit;
        }

        return $results;
    }

    public static function getProyects($integradoId = null, $projectId = null){
        $where = null;

        if(!is_null($integradoId)){
            $where = 'parentId = 0 AND integradoId = '.$integradoId;
        }elseif(!is_null($projectId)){
            $where = 'id_proyecto = '.$projectId;
        }

        $respuesta = self::selectDB('integrado_proyectos',$where,'id_proyecto');

        return $respuesta;
    }

    public static function getAllSubProyects($idProy = null){
        $respuesta = null;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if( is_null($idProy) ){
            $query->select($db->quoteName('t2.id_proyecto').', '.$db->quoteName('t2.integradoId').', '.$db->quoteName('t2.parentId').', '.$db->quoteName('t2.name').', '.$db->quoteName('t2.description').', '.$db->quoteName('t2.status') )
                ->from($db->quoteName('#__integrado_proyectos', 't1'))
                ->join('LEFT', $db->quoteName('#__integrado_proyectos', 't2') . ' ON (' . $db->quoteName('t2.parentId') . ' = ' . $db->quoteName('t1.id_proyecto') . ')');
        }else{
            $query->select($db->quoteName('t2.id_proyecto').', '.$db->quoteName('t2.integradoId').', '.$db->quoteName('t2.parentId').', '.$db->quoteName('t2.name').', '.$db->quoteName('t2.description').', '.$db->quoteName('t2.status') )
                ->from($db->quoteName('#__integrado_proyectos', 't1'))
                ->join('LEFT', $db->quoteName('#__integrado_proyectos', 't2') . ' ON (' . $db->quoteName('t2.parentId') . ' = ' . $db->quoteName('t1.id_proyecto') . ')')
                ->where($db->quoteName('t2.parentId').' = '.$db->quote($idProy));
        }


        try{
            $db->setQuery($query);
            $result = $db->loadObjectList();
        }catch (Exception $e){
            var_dump($e);
        }


        foreach ($result as $value) {
            if(!is_null($value->parentId)){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getActiveProyects($integradoId = null, $projectId = null){
        $where = null;

        if(!is_null($integradoId)){
            $where = 'parentId = 0 AND status = 1 AND integradoId = '.$integradoId;
        }elseif(!is_null($projectId)){
            $where = 'status = 1 AND id_proyecto = '.$projectId;
        }

        $respuesta = self::selectDB('integrado_proyectos',$where,'id_proyecto');

        return $respuesta;
    }

    public static function getActiveSubProyects($idProy = null){
        $respuesta = null;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if( is_null($idProy) ){
            $query->select($db->quoteName('t2.id_proyecto').', '.$db->quoteName('t2.integradoId').', '.$db->quoteName('t2.parentId').', '.$db->quoteName('t2.name').', '.$db->quoteName('t2.description').', '.$db->quoteName('t2.status') )
                ->from($db->quoteName('#__integrado_proyectos', 't1'))
                ->join('LEFT', $db->quoteName('#__integrado_proyectos', 't2') . ' ON (' . $db->quoteName('t2.parentId') . ' = ' . $db->quoteName('t1.id_proyecto') . ')')
                ->where($db->quoteName('t2.status') .'= 1');
        }else{
            $query->select($db->quoteName('t2.id_proyecto').', '.$db->quoteName('t2.integradoId').', '.$db->quoteName('t2.parentId').', '.$db->quoteName('t2.name').', '.$db->quoteName('t2.description').', '.$db->quoteName('t2.status') )
                ->from($db->quoteName('#__integrado_proyectos', 't1'))
                ->join('LEFT', $db->quoteName('#__integrado_proyectos', 't2') . ' ON (' . $db->quoteName('t2.parentId') . ' = ' . $db->quoteName('t1.id_proyecto') . ')')
                ->where($db->quoteName('t2.parentId').' = '.$db->quote($idProy) . ' AND ' . $db->quoteName('t2.status') .' = 1');
        }

        try{
            $db->setQuery($query);
            $result = $db->loadObjectList();
        }catch (Exception $e){
            var_dump($e);
        }

        foreach ($result as $value) {
            if(!is_null($value->parentId)){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getProducts($integradoId = null, $productId = null, $status = null){
        $where = null;

        if(is_null($integradoId) && is_null($productId)){
            $where = null;
        }elseif(!is_null($integradoId) && is_null($productId)){
            $where = 'integradoId = '.$integradoId;
        }elseif(!is_null($productId) && is_null($integradoId)){
            $where = 'id_producto = '.$productId;
        }

        if(!is_null($status) ){
            $where .= ' AND status = '.$status;
        }

        $respuesta = self::selectDB('integrado_products',$where);

        return $respuesta;
    }

    public static function getClientes($userId = null, $type = 2){
        $db       = JFactory::getDbo();
        $query    = $db->getQuery(true);

        if( !is_null($userId) ) {
            //Obtiene todos los id de los clientes/proveedores dados de alta para un integrado
            $query->select('id AS client_id, integradoIdCliente AS id, tipo_alta AS type, integrado_id, status, bancos AS bancoIds')
                ->from('#__integrado_clientes_proveedor')
                ->where('integrado_Id = ' . $userId);
            try {
                $db->setQuery($query);
                $response = $db->loadObjectList();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            //Obtiene los datos personales y de empresa del cliente/proveedor
            foreach ($response as $value) {
                $db = JFactory::getDbo();
                $querygral = $db->getQuery(true);

                $querygral->select('DE.rfc, DP.rfc as pRFC, DP.nom_comercial AS tradeName, DE.razon_social AS corporateName, DP.nombre_representante AS contact')
                    ->from('#__integrado_datos_personales AS DP')
                    ->join('LEFT', $db->quoteName('#__integrado_datos_empresa', 'DE') . ' ON (' . $db->quoteName('DE.integrado_id') . ' = ' . $db->quoteName('DP.integrado_id') . ')')
                    ->where('DP.integrado_id = ' . $value->id);

                try {
                    $db->setQuery($querygral);
                    $general = $db->loadObject();

                    $value->rfc = @$general->rfc;
                    $value->pRFC = @$general->pRFC;
                    $value->tradeName = @$general->tradeName;
                    $value->corporateName = @$general->corporateName;
                    $value->contact = @$general->contact;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }


            //obtiene los datos de contacto para el cliente/proveedor
            foreach ($response as $value) {
                $db = JFactory::getDbo();
                $queryphone = $db->getQuery(true);

                $queryphone->select('*')
                    ->from('#__integrado_contacto')
                    ->where('integrado_id = ' . $value->id);

                try {
                    $db->setQuery($queryphone);
                    $phone = $db->loadObject();
                    $value->phone = isset($phone->telefono) ? $phone->telefono : '';
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

            //obtengo los datos de cuentas bancarias del cliente;
            foreach ($response as $value) {
                if(!is_null($value->bancoIds)) {
                    $arrayBancoIds = array_filter(json_decode($value->bancoIds, true));
                    $bancoIds = !empty($arrayBancoIds) ? ' IN (' . implode(',', $arrayBancoIds) . ')' : null;

                    if (!is_null($bancoIds)) {
                        $db = JFactory::getDbo();
                        $querybanco = $db->getQuery(true);

                        $querybanco->select('*')
                            ->from('#__integrado_datos_bancarios')
                            ->where('datosBan_id' . $bancoIds);

                        try {
                            $db->setQuery($querybanco);
                            $banco = $db->loadObjectList();
                            $value->bancos = $banco;
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }

            foreach ($response as $key => $value) {
                if ( isset( $value->bancos ) ) {
                    foreach ($value->bancos as $indice => $valor) {
                        $valor->banco_cuenta_xxx = 'XXXXXX' . substr($valor->banco_cuenta, -4, 4);
                        $valor->banco_clabe_xxx = 'XXXXXXXXXXXXXX' . substr($valor->banco_clabe, -4, 4);
                    }
                }
            }
        }else{
            //Se regresan los datos de los clientes/proveedores dados de alta.
            $query->select('clientes.id AS client_id, clientes.integradoIdCliente AS idCliPro, clientes.integrado_Id AS integradoId, clientes.tipo_alta AS type, clientes.monto, clientes.status,
                            DP.nom_comercial AS dp_con_comercial, DP.nombre_representante AS dp_nom_representante, DP.rfc AS dp_rfc, DP.curp AS dp_curp,
                            DE.razon_social AS de_razon_social, DE.rfc AS de_rfc')
                ->from('#__integrado_clientes_proveedor AS clientes')
                ->join('INNER','#__integrado_datos_personales AS DP on clientes.integradoIdCliente = DP.integrado_id')
                ->join('INNER', '#__integrado_datos_empresa as DE on clientes.integradoIdCliente = DE.integrado_id')
                ->order('clientes.integrado_Id, clientes.tipo_alta ASC');

            try{
                $db->setQuery($query);
                $listAllCliPro = $db->loadObjectList();
            }catch (Exception $e){
                echo $e->getMessage();
            }

            foreach ($listAllCliPro as $value) {
                $where = $db->quoteName('integrado_id').' = '.$value->idCliPro;
                $contacto   = self::selectDB('integrado_contacto', $where);
                $banco      = self::selectDB('integrado_datos_bancarios', $where);

                $value->datosBanco  = $banco;
                $value->contacto    = $contacto;
            }
            $response = $listAllCliPro;
        }
        $catalogo = new Catalogos();

        $clientes = array();
        $proveedores = array();

        if(!empty($response)){
            foreach ($response as $value) {

                if( in_array($value->type, $catalogo->clientTypes()) && in_array($type, $catalogo->clientTypes()) ){
                    $clientes[] = $value;
                }
                if( in_array($value->type, $catalogo->providerTypes()) && in_array($type, $catalogo->providerTypes()) ){
                    $proveedores[] = $value;
                }

            }
        }
        $response = array_merge($clientes, $proveedores);

        return $response;
    }


    public static function getRemainderOrder($idOrden, $tipoOrden, $montoTx){
        switch($tipoOrden){
            case 'odd':
                $orden = self::getOrdenesDeposito(null,$idOrden);
                break;
            case 'odc':
                $orden = self::getOrdenesCompra(null,$idOrden);
                break;
            case 'odr':
                $orden = self::getOrdenesRetiro(null,$idOrden);
                break;
            case 'odv':
                $orden = self::getOrdenesVenta(null,$idOrden);
                break;
        }
        $remainder = (FLOAT) $orden[0]->totalAmount-$montoTx;

        return $remainder;
    }

    public static function getAllOrders( $intergradoId = null ) {
        $orders = new stdClass();
        $orders->odd = self::getOrdenesDeposito($intergradoId);
        $orders->odv = self::getOrdenesVenta($intergradoId);
        $orders->odr = self::getOrdenesRetiro($intergradoId);
        $orders->odc = self::getOrdenesCompra($intergradoId);

        return $orders;
    }

    public static function getOrdersCxP( $intergradoId = null ){
        $orders = new stdClass();

        $orders->odc = self::getOrdenesCompra($intergradoId);

        if ( ! empty( $orders ) ) {
            foreach ( $orders as $key => $values ) {
                $orders->$key = self::filterOrdersByStatus($values, array(5,8));
            }
        }

        return $orders;
    }

    public static function getOrdersCxC( $intergradoId = null ){
        $orders = new stdClass();
        $orders->odv = self::getOrdenesVenta($intergradoId);

        if ( ! empty( $orders ) ) {
            foreach ( $orders as $key => $values ) {
                $orders->$key = self::filterOrdersByStatus($values, array(5,8));
            }
        }

        return $orders;
    }

    /**
     * @param $orders array
     * @param $statusId array
     */
    public static function filterOrdersByStatus( $orders, $statusId ){
        $resultados = array();

        foreach ( $orders as $key => $value ) {
            if ( isset( $value->status->id ) ) {
                if (in_array($value->status->id, $statusId)) {
                    $resultados[] = $value;
                }
            }
        }

        return $resultados;
    }

    public static function getOrdenes($integradoId = null, $idOrden = null, $table){
        $where = null;
        if(isset($idOrden)){
            $where = 'id = '.$idOrden;
        }elseif(isset($integradoId)){
            $where = 'integradoId = '.$integradoId;
        }
        $ordenes = self::selectDB($table, $where);

        if (!empty($ordenes)) {
            foreach ($ordenes as $orden) {
                self::convierteFechas($orden);
            }
        }
        return $ordenes;
    }

    public static function getOrdenesDeposito($integradoId = null, $idOrden = null){
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_deposito');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->integradoId     = (INT)$value->integradoId;
            $value->orderType       = 'odd';
            $value->numOrden        = (INT)$value->numOrden;
            $value->status          = (INT)$value->status;
            $value->paymentMethod   = (INT)$value->paymentMethod;
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->attachment      = (STRING)$value->attachment;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
            $receptor               = new IntegradoSimple($value->integradoId);
            $value->receptor        = $receptor->getDisplayName();

            $value->status = self::getOrderStatusName($value->status);
            $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);

            // TODO: Cambiar por metodo que busca los pagos asociados a la orden
            $o = new OrdenFn();
            $value->balance = $o->calculateBalance($value);
        }

        return $orden;
    }

    public static function getOrdenesRetiro($integradoId = null, $idOrden= null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_retiro');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->integradoId     = (INT)$value->integradoId;
            $value->orderType       = 'odr';
            $value->numOrden        = (INT)$value->numOrden;
            $value->paymentMethod   = (INT)$value->paymentMethod;
            $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);
            $value->status          = self::getOrderStatusName($value->status);
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
            $value->cuentaId        = 0;

            $integCurrent = new IntegradoSimple(JFactory::getSession()->get('integradoId',null,'integrado'));
            $value->cuenta = $integCurrent->integrados[0]->datos_bancarios[$value->cuentaId];

            $o = new OrdenFn();
            $value->balance = $o->calculateBalance($value);
        }

        return $orden;
    }

    public static function getOrdenesCompra($integradoId = null, $idOrden = null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_compra');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->orderType       = 'odc';
            $value->proyecto        = (INT)$value->proyecto;
            $value->clientId        = (INT)$value->proveedor;
            $value->proveedor       = (INT)$value->proveedor;
            $value->integradoId     = (INT)$value->integradoId;
            $value->numOrden        = (INT)$value->numOrden;
            $value->paymentMethod   = (INT)$value->paymentMethod;
            $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);
            $value->status          = (INT)$value->status;
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
            $value->urlXML          = (STRING)$value->urlXML;
            $value->observaciones   = (STRING)$value->observaciones;

            $value->bankId          = (INT)$value->bankId;
            $value->dataBank        = self::getDataBankByBankId($value->bankId);
            $value                  = self::getProviderFromID($value);
            $value->status          = self::getOrderStatusName($value->status);

            $xmlFileData            = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$value->urlXML);
            $data 			        = new xml2Array();
            $value->factura         = $data->manejaXML($xmlFileData);

            $value->subTotalAmount  = (float)$value->factura->comprobante['SUBTOTAL'];
            $value->totalAmount     = $value->factura->comprobante['TOTAL'];
            $value->iva             = $value->factura->impuestos->iva->importe;
            $value->ieps            = $value->factura->impuestos->ieps->importe;

            $o = new OrdenFn();
            $value->balance = $o->calculateBalance($value);

            $emisor = new IntegradoSimple($value->integradoId);
            $value->emisor = $emisor->getDisplayName();

            $value->receptor = new IntegradoSimple($value->proveedor->integrado_id);

            $proyectos = self::getProyects(null, $value->proyecto);

            if($proyectos[$value->proyecto]->parentId != 0){
                $value->subproyecto = $proyectos[$value->proyecto];
                $proyecto = self::getProyects(null, $proyectos[$value->proyecto]->parentId);
                $value->proyecto = $proyecto[$value->subproyecto->parentId];
            }else{
                $value->proyecto = $proyectos[$value->proyecto];
                $value->subproyecto = '';
            }
        }
        return $orden;
    }

    public static function getOrdenesVenta($integradoId = null, $idOrden = null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_venta');
        $catalogo = new Catalogos();
        $catalogoIva = $catalogo->getCatalogoIVA();

        //Cambio el tipo de dato para las validaciones con (===)
        foreach ($orden as $key => $value) {
            $value->id             = (INT)$value->id;
            $value->integradoId    = (INT)$value->integradoId;
            $value->orderType      = 'odv';
            $value->numOrden       = (INT)$value->numOrden;
            $value->proyecto       = (INT)$value->projectId2==0?$value->projectId:$value->projectId2;
            $value->clientId       = (INT)$value->clientId;
            $value->account        = (INT)$value->account;
            $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);
            $value->conditions     = (INT)$value->conditions;
            $value->placeIssue     = self::getNombreEstado($value->placeIssue);
            $value->status         = (INT)$value->status;
            $value->productos      = (STRING)$value->productos;
            $value->createdDate    = (STRING)$value->createdDate;
            $value->paymentDate    = (STRING)$value->paymentDate;

            $subTotalOrden        = 0;
            $subTotalIva          = 0;
            $subTotalIeps         = 0;

            $value->productosData = json_decode($value->productos);

            foreach ($value->productosData  as $producto ) {
                $producto->iva = $catalogoIva[$producto->iva]->leyenda;

                $subTotalOrden  = $subTotalOrden + $producto->cantidad * $producto->p_unitario;
                $subTotalIva    = $subTotalIva + ($producto->cantidad * $producto->p_unitario) * ($producto->iva/100);
                $subTotalIeps   = $subTotalIeps + ($producto->cantidad * $producto->p_unitario) * ($producto->ieps/100);
            }

            $value->subTotalAmount = (float)$subTotalOrden;
            $value->totalAmount    = $subTotalOrden + $subTotalIva + $subTotalIeps;
            $value->iva      = $subTotalIva;
            $value->ieps     = $subTotalIeps;

            $o = new OrdenFn();
            $value->balance = $o->calculateBalance($value);

            $value = self::getProyectFromOrder($value);
            $value = self::getClientFromID($value);
            $value->status = self::getOrderStatusName($value->status);
        }

        return $orden;
    }

    public static function getOrderStatusCatalog( ){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__catalog_order_status')
            ->order('id');
        $db->setQuery($query);

        $result = $db->loadObjectList('id');

        return $result;
    }

    public static function getOrderStatusCatalogByName( ) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__catalog_order_status')
            ->order('id');
        $db->setQuery($query);

        $result = $db->loadObjectList('name');

        return $result;
    }

    public static function getParametrosMutuo( ){
        return self::selectDB('mandatos_mutuos', null, 'id');
    }

    public static function getOrderStatusName($statusId){
        $where = null;

        $result = self::getOrderStatusCatalog();

        if(isset($statusId)) {
            if(array_key_exists($statusId, $result)) {
                $result = $result[$statusId];
            } else {
                $result = new stdClass();
                $result->id = $statusId;
                $result->name = 'Estatus inválido';
            }
        }

        return $result;
    }

    public static function getPaymentMethodName($paymentMethodId){
        $cat = new Catalogos();
        $names = $cat->getPaymentMethods();

        try {
            $payMethod = new stdClass();

            $payMethod->id   = $paymentMethodId;
            $payMethod->name = $names[ $paymentMethodId ]->tag;
        } catch ( Exception $e) {
            $payMethod = null;
            JFactory::getApplication()->enqueueMessage('ERR_PAYMENT_METHOD_INVALID');
        }

        return $payMethod;
    }

    public static function getProyectFromOrder($orden){
        $proyectos = self::getProyects($orden->integradoId);

        if(array_key_exists($orden->proyecto, $proyectos)) {
            $orden->proyecto = $proyectos[$orden->proyecto];

            if($orden->proyecto->parentId > 0) {
                $orden->sub_proyecto	= $orden->proyecto;
                $orden->proyecto		= $proyectos[$orden->proyecto->parentId];
            } else {
                $orden->subproyecto 	= null;
            }
        }

        $integ = new IntegradoSimple($orden->integradoId);
        $orden->integradoName = $integ->getDisplayName();

        return $orden;
    }


    public static function getClientFromID($orden){
        $proveedores = array();

        $clientes = self::getClientes($orden->integradoId);

        $catalogo = new Catalogos();

        foreach ($clientes as $key => $value) {
            if ( in_array($value->type, $catalogo->clientTypes()) ) {
                self::getClientProviderName($value);
                $proveedores[ $value->id ] = $value;
            }
        }

        $orden->proveedor = $proveedores[$orden->clientId];

        return $orden;
    }

    public static function getProviderFromID($orden){
        $proveedores = array();
        $catalogo = new Catalogos();
        $clientes = self::getClientes($orden->integradoId);

        foreach ($clientes as $key => $value) {
            if ( in_array($value->type,$catalogo->providerTypes()) ) {
                self::getClientProviderName($value);
                $proveedores[ $value->id ] = $value;
            }
        }

        if ( !isset($proveedores[$orden->clientId]) ) {
            $integ = new IntegradoSimple($orden->clientId);
            $proveedores[$orden->clientId] = $integ->integrados[0];
            $proveedores[$orden->clientId]->frontName = $integ->getDisplayName();
            $types = $catalogo->providerTypes();
            $proveedores[$orden->clientId]->type = $types[0];
            $proveedores[$orden->clientId]->contact = $integ->integrados[0]->datos_personales->nombre_representante;
            $proveedores[$orden->clientId]->pRFC = $integ->integrados[0]->datos_personales->rfc;
            $proveedores[$orden->clientId]->rfc = $integ->integrados[0]->datos_empresa->rfc;
            $proveedores[$orden->clientId]->phone = $integ->integrados[0]->datos_empresa->tel_fijo;
        }
        $orden->proveedor = $proveedores[$orden->clientId];

        return $orden;
    }

    public static function getOperacionesPorLiquidar($integradoId){
        $allOrdenes    = self::getOrdenesVenta($integradoId);
        $subTotalOrden = (FLOAT) 0;
        $subTotalIva   = (FLOAT) 0;
        $subTotalIeps  = (FLOAT) 0;
        $odvs = array();

        //Temporal en lo que se crean las facturas.
        foreach ($allOrdenes as $orden) {
            if($orden->status->id === OrdenFn::getStatusIdByName('pagada')){
                $orden->productos = json_decode($orden->productos);

                foreach ($orden->productos  as $producto ) {
                    $subTotalOrden  = (FLOAT)$subTotalOrden + $producto->cantidad * $producto->p_unitario;
                    $subTotalIva    = (FLOAT)$subTotalIva + ($producto->cantidad * $producto->p_unitario) * ($producto->iva/100);
                    $subTotalIeps   = (FLOAT)$subTotalIeps + ($producto->cantidad * $producto->p_unitario) * ($producto->ieps/100);
                }
                $total                 = $subTotalOrden + $subTotalIva + $subTotalIeps;
                $orden->subTotalAmount = $subTotalOrden;
                $orden->totalAmount    = $total;
                $orden->iva            = $subTotalIva;
                $orden->ieps           = $subTotalIeps;

                $odvs[] = $orden;

                $subTotalOrden = 0;
                $subTotalIva = 0;
                $subTotalIeps = 0;
            }
        }

        return $odvs;
    }

    /**
     * @param $allOdv array getFromTimone::getOperacionesPorLiquidar(integ_id)
     *
     * @return stdClass
     */
    public static function getSaldoOperacionesPorLiquidar($allOdv){
        $montoOperaciones   = new stdClass();

        $montoOperaciones->subtotalTotalOperaciones = 0;
        $montoOperaciones->totalImpuestos = 0;
        foreach ($allOdv as $orden) {
            $montoOperaciones->subtotalTotalOperaciones = $montoOperaciones->subtotalTotalOperaciones + $orden->subTotalAmount;
            $montoOperaciones->totalImpuestos = $montoOperaciones->totalImpuestos + $orden->iva + $orden->ieps;
        }

        return $montoOperaciones;
    }

    public static function getResultados($integradoId)
    {
        $respuesta = null;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1388880000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1393718400000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1396137600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1398816000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1401408000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;


        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1404086400000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1409356800000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1412035200000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1414627200000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1417305600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1419897600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
    }

    public static function getFacturasPorCobrar() {

        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query
            ->select('*')
            ->from($db->quoteName('#__ordenes_venta'))
            ->where($db->quoteName('status') . ' =13');
        $db->setQuery($query);
        $results= $db->loadObjectList();
        return $results;
    }

    public static function getFacturasNoPagadasByIntegrado($integradoId) {

        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query
            ->select('*')
            ->from($db->quoteName('#__ordenes_venta', 'a'))
            ->join('INNER', $db->quoteName('#__facturasxcobrar', 'b') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.id_odv') . ')')
            ->where($db->quoteName('a.status') . ' <= 28 AND '. $db->quoteName('a.integradoId') .' = '. $db->quote($integradoId));
        $db->setQuery($query);
        $results= $db->loadObjectList();

        return $results;
    }

    public static function getFacturasComision($integradoId=null, $idFactura=null){
        $where = null;
        if(!is_null($idFactura)){
            $where = 'id = '.$idFactura;
        }elseif(!is_null($integradoId)){
            $where = 'integradoId = '.$integradoId;
        }
        $facturas = self::selectDB('facturas_comisiones', $where);

        return $facturas;
    }

    public static function getFactComisiones(){
        $factComiciones = new stdClass();

        $factComiciones->id             = 1;
        $factComiciones->receptor       = 1;
        $factComiciones->emisor         = 'INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV';
        $factComiciones->created        = 1408632474029;
        $factComiciones->totalAmount    = 100000;

        $array[] = $factComiciones;

        $factComiciones = new stdClass();

        $factComiciones->id             = 34;
        $factComiciones->receptor       = 2;
        $factComiciones->emisor         = 'INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV';
        $factComiciones->created        = 1408632474129;
        $factComiciones->totalAmount    = 500000;

        $array[] = $factComiciones;

        foreach ($array as $key => $value) {
            self::convierteFechas($value);
            $respuesta[] = $value;
        }
        return $respuesta;

    }

    public static function getNumCuenta () {
        $respuesta = null;

        $cuentas = new stdClass;
        $cuentas->id = 1;
        $cuentas->numCuenta = '00743616552851576216';
        $cuentas->banco = '40012';

        $respuesta[] = $cuentas;

        $cuentas = new stdClass;
        $cuentas->id = 2;
        $cuentas->numCuenta = '00743616552851576054';
        $cuentas->banco = '40012';

        $respuesta[] = $cuentas;

        $cuentas = new stdClass;
        $cuentas->id = 3;
        $cuentas->numCuenta = '00743616552851576069';
        $cuentas->banco = '40021';

        $respuesta[] = $cuentas;

        $cuentas = new stdClass;
        $cuentas->id = 4;
        $cuentas->numCuenta = '00743616552851576691';
        $cuentas->banco = '40036';

        $respuesta[] = $cuentas;

        return $respuesta;
    }

    public static function getTxSinMandato($integradoId = null) {
        $where = 'idOrden IS NULL';
        if(!is_null($integradoId)) {
            $where = $where.' AND idIntegrado = '.$integradoId;
        }

        $txs = getFromTimOne::selectDB( 'txs_timone_mandato', $where );

        foreach ( $txs as $transaction ) {
            $transaction->data = getFromTimOne::getTxDataByTxId($transaction->idTx);
        }

        return $txs;
    }

    /**
     * @param $txUUID
     *
     * @return mixed
     */
    public static function getTxDataByTxId($txUUID) {

        // TODO: traer los datos de la Tx desde TimOne
        $timone = new TimOneRequest();

        $results = $timone->getTxDetails($txUUID);

        return $results;
    }

    public static function getMedidas(){
        $respuesta['litros'] 			= 'litros';
        $respuesta['Metros'] 			= 'Metros';
        $respuesta['Metros Cúbicos'] 	= 'Metros Cúbicos';

        return $respuesta;
    }

    public static function convierteFechas($objeto){
        foreach ($objeto as $key => $value) {
            if( is_numeric(strpos(strtolower($key),'date')) ){
                $objeto->$key = date('d-m-Y', ($value) );

                $objeto->timestamps = new stdClass();
                $objeto->timestamps->$key = (INT)$value;
            }
        }

    }

    public static function token(){
        $token = 'fghgjsdatr';
        return $token;
    }

    public static function newintegradoId($envio){
        $createdDate = time();
        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query->insert($db->quoteName('#__integrado'))
            ->columns($db->quoteName('status').', '.$db->quoteName('pers_juridica').', '.$db->quoteName('createdDate'))
            ->values($db->quote(0).','.$db->quote($envio).', '.$createdDate);

        $db->setQuery($query);
        $db->execute();
        $newId = $db->insertid();

        return $newId;
    }

    public static function getTxSTPbyRef( $id ) {
        $txs = getFromTimOne::selectDB( 'txs_timone_mandato', 'id = '.(int)$id );

        foreach ( $txs as $transaction ) {
            $transaction->data = getFromTimOne::getTxDataByTxId($transaction->idTx);
        }

        return $txs[0];
    }

    public static function getOperacionesVenta($integradoId){

        $operaciones 					= new stdClass;
        $operaciones->id                = 1;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 1;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 13000;
        $operaciones->currency        	= 'MXN';
        $operaciones->status            = 1;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        $operaciones 					= new stdClass;
        $operaciones->id                = 2;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 2;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 10000;
        $operaciones->currency        	= 'MXN';
        $operaciones->status            = 1;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        $operaciones 					= new stdClass;
        $operaciones->id                = 3;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 3;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 113000;
        $operaciones->currency        	= 'MXN';
        $operaciones->paymentType		= 0;
        $operaciones->status            = 0;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getFacturasVenta($integradoId){
        $odvs = self::getOrdenesVenta($integradoId);
        $facturas = array();

        foreach ($odvs as $odv) {
            if($odv->status->name === 'Autorizada'){
                $facturas[] = $odv;
            }
        }

        return $facturas;
    }

    public static function getComisiones($id = null) {
        $request = new getFromTimOne();

        $where = null;
        if(!is_null($id)) {
            $where = 'id = '.$id;
        }
        $comisiones = $request->selectDB('mandatos_comisiones', $where);

        return $comisiones;
    }

    public static function getComisionesOfIntegrado($integradoId) {
        $comisiones = null;
        $request = new getFromTimOne();

        $where = null;
        if(!is_null($integradoId)) {
            $where = 'integradoId = '.$integradoId;
        }
        $comisionesInteg = $request->selectDB('integrado_comisiones', $where);

        foreach ( $comisionesInteg as $valor ) {
            $result = $request->getComisiones($valor->comisionId);
            $comisiones[] = isset($result[0])?$result[0]:null;
        }
        return $comisiones;
    }

    public static function getTriggersComisiones() {
        $triggers = array('oddpagada' => 'Orden de Depósito pagada', 'odcpagada' => 'Orden de Compra pagada', 'fecha' => 'Según recurrencia');
        $eve = new comisionEvent();
        $tmp = $eve->getAll();

        foreach ( $tmp as $value ) {
            $trigg[$value->trigger] = $value->eventFullName;
        }
        $triggers = $trigg;

        return $triggers;
    }

    public static function calculaComision( $orden, $tipoOrden, $comisiones ) {

        $comision = self::getAplicableComision($tipoOrden, $comisiones);

        // TODO: verificar $orden->totalAmount con el comprobante del xml
        // TODO: agregar una variable que indique el IVA que se va a ocupar y quitar el valor harcodeado.
        $catalogo = new Catalogos();

        $ivas = (int)$catalogo->getFullIva();

        $montoComision = isset($comision) ? $orden->totalAmount * ($comision->rate / 100) * (1+(ivas/100)) : null;

        return $montoComision;
    }

    public function getUnpaidOrderStatusCatalog() {
        $statuses = self::getOrderStatusCatalog();

        try {
            if(!array_key_exists(13, $statuses)) {
                throw new Exception('Código 2001');
            }
        }
        catch (Exception $e) {
            JFactory::getApplication()->redirect('index.php','Error: '.$e->getMessage());
        }

        foreach ( $statuses as $status ) {
            if($status->name === 'Pagada' ) {
                $pagadaStatusId = $status->id;
            }
        }

        foreach ( $statuses as $id => $status ) {
            if ($status->id < $pagadaStatusId) {
                $results[] = $status->id;
            }
        }

        return $results;
    }

}

class sendToTimOne {

    public $result;
    protected $httpType;
    protected $serviceUrl;
    protected $jsonData;
    protected $integradoId;

    function __construct () {
        $this->serviceUrl   = null;
        $this->jsonData     = null;
        $this->setHttpType('GET');
        $session = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null, 'integrado');
    }

    public static function uploadFiles($integradoId = null) {
        $save = array();
        $db 	= JFactory::getDbo();

        $data	= JFactory::getApplication()->input->getArray();

        if ( isset( $data['integradoId'] ) ) {
            $integrado_id = $data['integradoId'] != '' ? $data['integradoId'] : '';
        } elseif ( isset($integradoId) ) {
            $integrado_id = $integradoId;
        } else {
            $integrado_id = '';
        }

        foreach ($_FILES as $key => $value) {
            $result = manejoImagenes::cargar_imagen($value['type'], $integrado_id, $value, $key);

            if ($result != 'verificar') {
                $fileinfo = pathinfo( $value['name'] );

                $columna = substr( $key, 3 );
                $clave   = substr( $key, 0, 3 );
                $where   = $db->quoteName( 'integrado_id' ) . ' = ' . $integrado_id;

                switch ( $clave ) {
                    case 'dp_':
                        $table = 'integrado_datos_personales';
                        break;
                    case 'de_':
                        $table = 'integrado_datos_empresa';
                        break;
                    case 'db_':
                        $table = 'integrado_datos_bancarios';
                        break;
                    case 't1_':
                        $table = 'integrado_instrumentos';
                        $where = $db->quoteName( 'integrado_id' ) . ' = ' . $integrado_id . ' AND ' . $db->quoteName( 'instrum_type' ) . ' = 1';
                        break;
                    case 't2_':
                        $table = 'integrado_instrumentos';
                        $where = $db->quoteName( 'integrado_id' ) . ' = ' . $integrado_id . ' AND ' . $db->quoteName( 'instrum_type' ) . ' = 2';
                        break;
                    case 'pn_':
                        $table = 'integrado_instrumentos';
                        $where = $db->quoteName( 'integrado_id' ) . ' = ' . $integrado_id . ' AND ' . $db->quoteName( 'instrum_type' ) . ' = 3';
                        break;
                    case 'rp_':
                        $table = 'integrado_instrumentos';
                        $where = $db->quoteName( 'integrado_id' ) . ' = ' . $integrado_id . ' AND ' . $db->quoteName( 'instrum_type' ) . ' = 4';
                        break;

                    default:

                        break;
                }
                $updateSet = array( $db->quoteName( $columna ) . ' = ' . $db->quote( "media/archivosJoomla/" . $integrado_id . '_' . $key . "." . strtolower($fileinfo['extension']) ) );

                $save[] = self::updateDB( $table, $updateSet, $where );
            }else {
                $campos[] = $key;
            }
        }

        $integrado = new IntegradoSimple($integrado_id);
        $integrado = $integrado->integrados[0];

        if($integrado->integrado->pers_juridica == 2){
            $evaluacion['dp_url_identificacion'] = $integrado->datos_personales->url_identificacion;
            $evaluacion['dp_url_rfc'] = $integrado->datos_personales->url_rfc;
            $evaluacion['dp_url_comprobante_domicilio'] = $integrado->datos_personales->url_comprobante_domicilio;


        }

        foreach($campos as $value){
            $clave   = substr( $value, 0, 3 );
            $columna = substr( $value, 3 );

            switch($clave){
                case 'dp_':
                    is_null($integrado->datos_personales->$columna)?JFactory::getApplication()->enqueueMessage('Falta '.$columna.' o el formato del archivo es incorrecto'):'';
                    break;
                case 'de_':
                    if($integrado->integrado->pers_juridica == 1){
                        is_null($integrado->datos_empresa->url_rfc)?JFactory::getApplication()->enqueueMessage('Falta comprobante de RFC de Empresa o el formato del archivo es incorrecto'):'';
                    }
                    break;
                case 't1_':
                    if($integrado->integrado->pers_juridica == 1) {
                        is_null($integrado->testimonio1->url_instrumento) ? JFactory::getApplication()->enqueueMessage('Falta compribante del testimonio 1 o el formato del archivo es incorrecto') : '';
                    }
                    break;
                case 't2_':
                    if($integrado->integrado->pers_juridica == 1) {
                        is_null($integrado->testimonio2->url_instrumento) ? JFactory::getApplication()->enqueueMessage('Falta comprobante del testimonio 2 o el formato del archivo es incorrecto') : '';
                    }
                    break;
                case 'pn_':
                    if($integrado->integrado->pers_juridica == 1) {
                        is_null($integrado->poder->url_instrumento) ? JFactory::getApplication()->enqueueMessage('Falta comprobante del poder notarial o el formato del archivo es incorrecto') : '';
                    }
                    break;
                case 'rp_':
                    if($integrado->integrado->pers_juridica == 1) {
                        is_null($integrado->reg_propiedad->url_instrumento) ? JFactory::getApplication()->enqueueMessage('Falta comprobante del Registro publico de propiedad o el formato del archivo es incorrecto ') : '';
                    }
                    break;
                default:
                    break;
            }
        }

        return !in_array(false, $save);

    }

    public static function getTableByType($tipo)
    {
        switch($tipo){
            case 'odd':
                $table = 'ordenes_deposito';
                break;
            case 'odv':
                $table = 'ordenes_venta';
                break;
            case 'odc':
                $table = 'ordenes_compra';
                break;
            case 'odr':
                $table = 'ordenes_retiro';
                break;
            case 'odp':
                $table = 'ordenes_prestamo';
                break;
            case 'mutuo':
                $table = 'mandatos_mutuos';
                break;
            case 'odd_auth':
                $table = 'auth_odd';
                break;
            case 'odv_auth':
                $table = 'auth_odv';
                break;
            case 'odc_auth':
                $table = 'auth_odc';
                break;
            case 'odr_auth':
                $table = 'auth_odr';
                break;
            case 'odp_auth':
                $table = 'auth_odp';
                break;
            case 'mutuo_auth':
                $table = 'auth_mutuo';
                break;
        }

        return $table;
    }

    public function getNextOrderNumber($tipo, $integrado){
        $db		= JFactory::getDbo();

        $table = self::getTableByType($tipo);

        $where = $db->quoteName('integradoId').' = '.$integrado;

        $query 	= $db->getQuery(true);

        $query->select('max(numOrden) AS lastOrderNum')
            ->from($db->quoteName('#__'.$table))
            ->where($where);

        $db->setQuery($query);
        $resultado = $db->loadObject();

        $respuesta = is_null($resultado->lastOrderNum) ? 1 : $resultado->lastOrderNum + 1;

        return $respuesta;
    }

    public function formatData($arreglo){
        $db		= JFactory::getDbo();

        $this->columnas = null;
        $this->valores = null;
        $this->set = null;
        foreach ($arreglo as $key => $value) {
            $this->columnas[] = $key;
            $this->valores[] = $db->quote($value);
            $this->set[] = $db->quoteName($key).' = '.$db->quote($value);
        }
    }

    public function saveProject($data){
        $db		= JFactory::getDbo();
        foreach ($data as $key => $value) {
            $columnas[] = $key;
            $valores[] = $db->quote($value);
        }

        $projectId = $this->insertDB('integrado_proyectos', $columnas, $valores, true);

        return $projectId;
    }

    public function saveODP($data){
        $db		= JFactory::getDbo();

        foreach ($data as $key => $value) {
            $columnas[] = $key;
            $valores[] = $db->quote($value);
        }

        $odpId = $this->insertDB('ordenes_prestamo', $columnas, $valores, true);

        return $odpId;
    }

    public function updateProject($data,$id_proyecto){
        $db		= JFactory::getDbo();
        foreach ($data as $key => $value) {
            $columnas[] = $db->quoteName($key).'= '.$db->quote($value);
        }
        $condicion = $db->quoteName('id_proyecto').' = '.$id_proyecto;

        $this->updateDB('integrado_proyectos', $columnas, $condicion);
    }

    public function saveProduct($data){
        $db		= JFactory::getDbo();
        foreach ($data as $key => $value) {
            $columnas[] = $key;
            $valores[] = $db->quote($value);
        }
        $this->insertDB('integrado_products', $columnas, $valores);
    }

    public function updateProduct($data, $id_producto){
        $db		= JFactory::getDbo();
        foreach ($data as $key => $value) {
            $columnas[] = $db->quoteName($key).'= '.$db->quote($value);
        }
        $condicion = $db->quoteName('id_producto').' = '.$id_producto;

        $this->updateDB('integrado_products', $columnas, $condicion);
    }

    public function sendDataTIMONE(){

    }

    public function insertDB($tabla, $columnas = null, $valores = null, $last_inserted_id = null){
        $scope = JFactory::getApplication()->scope;
        JLog::addLogger(array('text_file' => date('d-m-Y').'_'.$scope.'_errors.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::ALL);

        $columnas = is_null($columnas) ? $this->columnas : $columnas;
        $valores = is_null($valores) ? $this->valores : $valores;

        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query->insert($db->quoteName('#__'.$tabla))
            ->columns($db->quoteName($columnas))
            ->values(implode(',',$valores));

        try{
            $db->setQuery($query);
            $db->execute();
            $return = true;
        }catch (Exception $e){
            $logdata = implode(' | ',array(JFactory::getUser()->id, $this->integradoId, __METHOD__.':'.__LINE__, json_encode( $e->getMessage() ) ) );
            JLog::add($logdata,JLog::ERROR,'Error INTEGRADORA DB');
            $return = false;
        }

        if( ($last_inserted_id) && ($return)){
            $return= $db->insertid();
        }

        return $return;
    }

    public function updateDB($table, $set=null, $condicion=null){
        $scope = JFactory::getApplication()->scope;
        JLog::addLogger(array('text_file' => date('d-m-Y').'_'.$scope.'_errors.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::ALL & ~JLog::WARNING & ~JLog::INFO & ~JLog::DEBUG);

        $set = is_null($set) ? $this->set : $set;

        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query->update($db->quoteName('#__'.$table))
            ->set(implode(',', $set))
            ->where($condicion);

        try {
            $db->setQuery($query);
            $db->execute();
            $return = true;
        }catch (Exception $e){
            $logdata = implode(' | ',array(JFactory::getUser()->id, $this->integradoId, __METHOD__.':'.__LINE__, json_encode( $e->getMessage() ) ) );
            JLog::add($logdata,JLog::ERROR,'Error INTEGRADORA DB');
            $return = false;
        }

        return $return;
    }

    public function deleteDB($table, $condicion){
        JLog::addLogger(array());
        $return = '';
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->delete($db->quoteName('#__' . $table))
                ->where($condicion);

            $db->setQuery($query);
            $db->execute();

            $return = true;

        }catch (Exception $e){
            JLog::add($e->getMessage(),JLog::ERROR,'Error INTEGRADORA'.__METHOD__);
            $return = $e->getMessage();

        }
        return $return;
    }

    /**
     * @param mixed $serviceUrl
     */
    public function setServiceUrl ($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * @return mixed
     */
    public function getServiceUrl () {
        return $this->serviceUrl;
    }

    /**
     * @param mixed $jsonData
     */
    public function setJsonData ($jsonData) {
        $this->jsonData = $jsonData;
    }

    /**
     * @return mixed
     */
    public function getJsonData () {
        return $this->jsonData;
    }

    public function to_timone() {

        $verboseflag = true;
//		$credentials = array('username' => '' ,'password' => '');
        $verbose = fopen(JFactory::getConfig()->get('log_path').'/curl-'.date('d-m-y').'.log', 'a+');
        $ch = curl_init();

        switch($this->getHttpType()) {
            case ('POST'):
                $options = array(
                    CURLOPT_POST 		    => true,
                    CURLOPT_URL            => $this->serviceUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS     => $this->jsonData,
                    CURLOPT_HEADER         => false,
                    //			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_VERBOSE        => $verboseflag,
                    CURLOPT_STDERR		   => $verbose,
                    CURLOPT_HTTPHEADER	   => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($this->jsonData)
                    )
                );
                break;
            case ('PUT'):
                $options = array(
                    CURLOPT_PUT 			=> true,
                    CURLOPT_URL            => $this->serviceUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HEADER         => true,
                    //			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_VERBOSE        => $verboseflag,
                    CURLOPT_STDERR		   => $verbose,
                    CURLOPT_HTTPHEADER	   => array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($this->jsonData)
                    )
                );
                break;
            case 'DELETE':
                $options = array(
                    CURLOPT_CUSTOMREQUEST => "DELETE",
                    CURLOPT_URL            => $this->serviceUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HEADER         => true,
                    //			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_VERBOSE        => $verboseflag,
                    CURLOPT_STDERR		   => $verbose,
                    CURLOPT_HTTPHEADER	   => array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($this->jsonData)
                    )
                );
                break;
            default:
                $options = array(
                    CURLOPT_HTTPGET			=> true,
                    CURLOPT_URL            => $this->serviceUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HEADER         => false,
                    //			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_VERBOSE        => $verboseflag,
                    CURLOPT_STDERR		   => $verbose,
                    CURLOPT_HTTPHEADER	   => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($this->jsonData)
                    )
                );
                break;
        }

        curl_setopt_array($ch,$options);

        if($verboseflag === true) {
            $headers = curl_getinfo( $ch,
                CURLINFO_HEADER_OUT );
            $this->result->data = curl_exec($ch);

            rewind( $verbose );
            $verboseLog = stream_get_contents( $verbose );
            //echo "Verbose information:\n<pre>", htmlspecialchars( $verboseLog ), "</pre>\n" . curl_errno( $ch ) . curl_error( $ch );
        }

        $this->result->code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
        $this->result->info = curl_getinfo ($ch);
        curl_close($ch);

        JLog::add(json_encode($this), JLog::DEBUG);

        switch ($this->result->code) {
            case 200:
                $this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_GRANTED');
                break;
            case 401:
                $this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_DENIED');
                break;
            default:
                $this->result->message = JText::_('JGLOBAL_AUTH_UNKNOWN_ACCESS_DENIED');
                break;
        }

        return $this->result;
    }

    public function setHttpType ($type) {
        if(in_array($type, array('PUT', 'POST', 'GET', 'PATCH', 'DELETE'))) {
            $this->httpType = $type;
        } else {
            return false;
        }
    }

    public function getHttpType () {
        return strtoupper($this->httpType);
    }

    public function changeOrderStatus($idOrder, $orderType, $orderNewStatus)
    {
        $return = false;

        $integradoId = JFactory::getSession()->get('integradoId', null, 'integrado');

        $order = getFromTimOne::getOrdenes($integradoId, $idOrder, self::getTableByType($orderType));
        $order = $order[0];

        $auths = OrdenFn::getCantidadAutRequeridas(new IntegradoSimple( OrdenFn::getIdEmisor($order, $orderType) ), new IntegradoSimple( OrdenFn::getIdReceptor($order, $orderType) ));
        switch ($orderType) {
            case 'odv':
                $order->cantidadAuthNecesarias = $auths->emisor;
                break;
            case 'odc':
                $order->cantidadAuthNecesarias = $auths->receptor;
                break;
            default:
                $order->cantidadAuthNecesarias = $auths->totales;
                break;
        }

        $tableAuth = $orderType.'_auth';
        $order->auths = getFromTimOne::getOrdenAuths($order->id, $tableAuth);

        $order->hasAllAuths = $order->cantidadAuthNecesarias == count($order->auths);
        $order->canChangeStatus = $this->validStatusChange($order, $orderNewStatus);

        if ( $order->status == 1 && !$order->canChangeStatus && $orderNewStatus == 5 ) {
            // pasa de esatus 1 (Nuevo) a 3 (En Autorizacion) en caso que no tiene las autrizaciones completas
            $orderNewStatus = 3;
            $order->hasAllAuths = true;
            $order->canChangeStatus = true;
        }

        if ($order->canChangeStatus) {
            $this->formatData(array('status' => $orderNewStatus ));
            $return = $this->updateDB(self::getTableByType($orderType),null, 'id ='.$order->id);

            $this->formatData(array('idOrden'=> $order->id,
                'userId' => JFactory::getUser()->id,
                'changeDate'=> time(),
                'pastStatus' => $order->status ,
                'newStatus'=> $orderNewStatus,
                'result' => $return
            ));

            $bitacora = $this->insertDB('bitacora_status_'.$orderType);
        }

        return $return;
    }

    private function validStatusChange($order,$orderNewStatus) {
        $return = false;

        switch ((INT)$order->status) {
            case 1:
                $return = in_array($orderNewStatus, array(3,5,8)) && $order->hasAllAuths;
                break;
            case 3:
                $return = in_array($orderNewStatus, array(5,55)) && $order->hasAllAuths;
                break;
            case 5:
                if($orderNewStatus < $order->status){
                    $return = $orderNewStatus == 3 && $order->hasAllAuths;
                }else {
                    $return = $orderNewStatus == 13 && $order->hasAllAuths;
                }
                break;
            case 8:
                if($orderNewStatus < $order->status){
                    $return = $orderNewStatus == 5 && $order->hasAllAuths;
                }else {
                    $return = $orderNewStatus == 13 && $order->hasAllAuths;
                }
        }

        return $return;
    }

    public static function referenciaTxMandato($txObject, $idOrden, $orderType) {
        switch ($orderType){
            case 'odc':
                $orders = getFromTimOne::getOrdenesCompra(null, $idOrden);
                break;
            case 'odv':
                $orders = getFromTimOne::getOrdenesVenta(null, $idOrden);
                break;
            case 'odd':
                $orders = getFromTimOne::getOrdenesDeposito(null, $idOrden);
                break;
        }
        $orders = $orders[0];

        $save = new sendToTimOne();

        $tx['idOrden'] = $orders->id;
        $tx['tipoOrden'] = $orderType;

        $save->formatData($tx);
        $save->updateDB('txs_timone_mandato',null, 'id = '.$txObject->id);

        $returnObj = getFromTimOne::getTxSTPbyRef($txObject->id);

        return $returnObj;
    }

    public function generarFacturaComisiones($dataFactura){
        // TODO: crear las facturas de comisiones
    }

    public function generaObjetoFactura( $newOrden, $timbra = true ) {

        $data = new Factura( $newOrden , $timbra);

        //TODO: qutar el mock cuando sea produccion
        if( ENVIROMENT_NAME == 'sandbox') {
            $data->setTestRFC();
        }

        return $data;
    }

    public function generateFacturaFromTimone(Factura $factura ) {

        return $factura->sendCreateFactura(); // realiza el envio
    }

	/**
	 * @param $data
	 *
	 * @param string $xmlpath
	 *
	 * @return bool|string filename {uuid}.xml
	 * xml
	 * @throws Exception
	 */
    public function saveXMLFile( $data, $xmlpath = XML_FILES_PATH ) {
	    $xmlpath = substr($xmlpath, -1) == '/' ? $xmlpath : $xmlpath.'/';

        if( !$result = simplexml_load_string ($data, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE) ) {
            throw new Exception('Error creando factura = '.$data);
        }

        $uuid = Factura::getXmlUUID($data);

        $filename = $xmlpath.$uuid.'.xml';
        $handle = fopen($filename, 'w');
        $write = fwrite($handle, $data);
        fclose($handle);

        if($write) {
            $return = $filename;
        } else {
            $return = false;
        }

        return $return;
    }

    public function changeProductStatus( $data ) {
        $response = false;

        self::formatData(array( 'status' => $data['status'] ));
        $result = self::updateDB('integrado_products', null, 'id_producto = '.$data['id_producto']);

        $product = getFromTimOne::getProducts(null, $data['id_producto']);
        $product = reset($product);

        if($result){
            $response['status'] = $product->status;
            $response['name'] = $product->productName;
            $response['statusName'] = ($product->status == 0) ? JText::_('JUNPUBLISHED') : JText::_('JPUBLISHED');
        }

        return $response;
    }

    public function changeProjectStatus( $data ) {
        $response = false;

        self::formatData(array( 'status' => $data['status'] ));
        $result = self::updateDB('integrado_proyectos', null, 'id_proyecto = '.$data['id_proyecto']);

        $project = getFromTimOne::getProyects(null, $data['id_proyecto']);
        $project = reset($project);

        if($result){
            $response['status'] = $project->status;
            $response['name'] = $project->name;
            $response['statusName'] = ($project->status == 0) ? JText::_('JUNPUBLISHED') : JText::_('JPUBLISHED');
        }

        return $response;
    }

    public function changeClientOrProviderStatus( $data ) {
        $response = false;

        self::formatData(array( 'status' => $data['status'] ));
        $result = self::updateDB('integrado_clientes_proveedor', null, 'id = '.$data['client_id']);

        $client = getFromTimOne::getClientProvider($data['client_id']);
        $client = reset($client);

        if($result){
            $response['status'] = $client->status;
            $response['name'] = !empty($client->de_razon_social) ? $client->de_razon_social : $client->dp_con_comercial;
            $response['statusName'] = ($client->status == 0) ? JText::_('JUNPUBLISHED') : JText::_('JPUBLISHED');
        }

        return $response;
    }

}

class comisionEvent {
    public $id;
    public $type;
    public $trigger;
    public $eventFullName;

    public function getAll() {
        $result = getFromTimOne::selectDB('catalog_comisiones_eventos', null, '', 'comisionEvent');

        return $result;
    }
}

/**
 * Class ReportBalance
 */
class ReportBalance extends IntegradoOrders {
    public $integradoId;
    public $retiros;
    public $depositos;
    public $capital;
    public $pasivo;
    public $observaciones;
    public $status;
    public $paymentType;
    public $currency;
    public $createdDate;
    public $proyectId;
    public $numBalance;
    public $id;
    public $period;
    public $year;
    public $activo;
    protected $request;

    /**
     * @param $params array(integradoId => $integradoId, balanceId  => $balanceId = null)
     */
    function __construct( $params ) {
        list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin();

        $this->request->integradoId = $params['integradoId'];

        if ( isset( $params['balanceId'] ) ) {
            if ( $params['balanceId'] != 0 ) {
                $this->request->balanceId   = $params['balanceId'];
            }
        }

        parent::__construct($params['integradoId']);
    }

    public function generateBalance( ) {
        $respuesta = null;

        $this->createData();
        getFromTimOne::convierteFechas( $this );
        $this->setDatesForDisplay();

    }

    public function getExistingBalance() {

        if ( ! empty( $this->request->integradoId ) && ! empty($this->request->balanceId) ) {
            $data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$this->request->integradoId.' AND id = '. $this->request->balanceId );
            list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin($data[0]->year);
        }

        $this->generateBalance();
    }

    /**
     * @internal param $b
     */
    public function createData() {
        // TODO: quitar simulados
        $this->createdDate                     = time();
        $this->year                            = 2013;
        $this->pasivo->cuentasPorPagar         = $this->getCxP()->neto;; // suma historica de CxP
        $this->pasivo->ivaVentas               = $this->getCxP()->iva;
        $this->pasivo->total                   = $this->pasivo->cuentasPorPagar + $this->pasivo->ivaVentas;
        $this->activo->bancoSaldoEndDate       = $this->getBancoSaldoEndDate();
        $this->activo->cuentasPorCobrar        = $this->getCxC()->neto;
        $this->activo->ivaCompras              = $this->getCxC()->iva;
        $this->activo->total                   = $this->activo->cuentasPorCobrar + $this->activo->ivaCompras + $this->activo->bancoSaldoEndDate;
        $this->capital->ejecicioAnterior       = 0;
        $this->capital->totalEdoResultados     = 750;
        $this->depositos->ejecicioAnterior     = 0;
        $this->depositos->actual               = 600;
        $this->retiros->ejecicioAnterior       = 0;
        $this->retiros->actual                 = 350;

        $this->capital->total                  = ($this->capital->ejecicioAnterior + $this->capital->totalEdoResultados + $this->depositos->ejecicioAnterior + $this->depositos->actual) - ($this->retiros->ejecicioAnterior + $this->retiros->actual);
    }

    private function getIvaVentasPeriodo( ) {
        $ivas = array();
        $invoices   = getFromTimOne::getOrdersCxC($this->request->integradoId);

        $filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

        $unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
        foreach ( $filteredOrders as $fact ) {
            $testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);

            $testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
            if ( $testStatus && $testDates) {
                $ivas[] = $fact->iva;
            }
        }

        return array_sum($ivas);
    }

    private function getIvaComprasPeriodo() {
        $ivas = array();
        $invoices   = getFromTimOne::getOrdersCxP($this->request->integradoId);

        $filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

        if ( ! empty( $filteredOrders ) ) {
            $respuesta = $this->sumOrders($filteredOrders);
        }
//		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
//		foreach ( $filteredOrders as $fact ) {
//			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);
//
//			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
//			if ( $testStatus && $testDates) {
//				$ivas[] = $fact->iva;
//			}
//		}

        return array_sum($ivas);
    }

    private function getCxP() {
        return $this->pasivo->data = $this->getData($this->orders->odc);
    }

    private function getCxC() {
        return $this->activo->data = $this->getData($this->orders->odv);
    }

    public function getData($Orders){
        $ordenesFiltradas = getFromTimOne::filterOrdersByStatus($Orders,array(5,8,13));
        $sumaOrdenes = getFromTimOne::sumaOrders($ordenesFiltradas);

        return $sumaOrdenes;
    }

    /**
     * @param null $year
     *
     * @return array
     */
    public function setDatesInicioFin( $year = null ) {
        $inicio = 'first day of January';
        $final = 'first day of this month';
        if (isset($year)) {
            $inicio = 'first day of January '.$year;
            $nextYear = (int)$year+1;
            $final = 'first day of January '.$nextYear;
        }
        $timeZone    = new DateTimeZone( 'America/Mexico_City' );
        $fechaInicio = new DateTime( $inicio, $timeZone );
        $fechaFin    = new DateTime( $final, $timeZone );
        $fechaFin->setTime( 0, 0, 0 );
        $fechaInicio->timestamp = $fechaInicio->getTimestamp();
        $fechaFin->timestamp    = $fechaFin->getTimestamp();

        return array ( $fechaInicio, $fechaFin );
    }

    private function setDatesForDisplay() {
        $this->period->startDate   = date('d-m-Y', $this->period->startDate->timestamp);
        $this->period->endDate     = date('d-m-Y', $this->period->endDate->timestamp);
    }

    private function getBancoSaldoEndDate() {
        // TODO: Operar el saldo con las Tx para sacar el saldo a cirre de periodo del balance
        return (float)946;
    }

    private function sumOrders( $orders ) {
        return getFromTimOne::sumaOrders($orders);
    }

    public static function getIntegradoExistingBalanceList($integradoId) {
        $data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$integradoId );

        return $data;
    }
}

class ReportBalanceTxs extends IntegradoTxs {
    public $integradoId;
    public $retiros;
    public $depositos;
    public $capital;
    public $pasivo;
    public $observaciones;
    public $status;
    public $paymentType;
    public $currency;
    public $createdDate;
    public $proyectId;
    public $numBalance;
    public $id;
    public $period;
    public $year;
    public $activo;
    protected $request;

    /**
     * @param $params array(integradoId => $integradoId, balanceId  => $balanceId = null)
     */
//    function __construct( $params ) {
//
//        $this->request->integradoId = $params['integradoId'];
//
//        if ( isset( $params['balanceId'] ) ) {
//            if ( $params['balanceId'] != 0 ) {
//                $this->request->balanceId   = $params['balanceId'];
//            }
//        }
//
//        parent::__construct($params['integradoId']);
//    }

    public function generateBalance( $year = null ) {
        list( $this->period->startDate, $this->period->endDate ) = $this->setDatesInicioFin($year);
        $respuesta = null;

        $this->txs = $this->getIntegradoTxs();

        $this->createData();
        getFromTimOne::convierteFechas( $this );
        $this->setDatesForDisplay();

    }

    /**
     * @internal param $b
     */
    public function createData() {
        // TODO: quitar simulados
        $this->createdDate                     = time();
        $this->year                            = $this->getYearFromPeriod();
        $this->pasivo->cuentasPorPagar         = $this->getCxP()->neto;; // suma historica de CxP
        $this->pasivo->ivaVentas               = $this->getCxP()->iva;
        $this->pasivo->total                   = $this->pasivo->cuentasPorPagar + $this->pasivo->ivaVentas;
        $this->activo->bancoSaldoEndDate       = $this->getBancoSaldoEndDate();
        $this->activo->cuentasPorCobrar        = $this->getCxC()->neto;
        $this->activo->ivaCompras              = $this->getCxC()->iva;
        $this->activo->total                   = $this->activo->cuentasPorCobrar + $this->activo->ivaCompras + $this->activo->bancoSaldoEndDate;
        $this->capital->ejecicioAnterior       = 0;
        $this->capital->totalEdoResultados     = 750;
        $this->depositos->ejecicioAnterior     = 0;
        $this->depositos->actual               = 600;
        $this->retiros->ejecicioAnterior       = 0;
        $this->retiros->actual                 = 350;

        $this->capital->total                  = ($this->capital->ejecicioAnterior + $this->capital->totalEdoResultados + $this->depositos->ejecicioAnterior + $this->depositos->actual) - ($this->retiros->ejecicioAnterior + $this->retiros->actual);
    }

    private function getIvaVentasPeriodo( ) {
        $ivas = array();
        $invoices   = getFromTimOne::getOrdersCxC($this->request->integradoId);

        $filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

        $unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
        foreach ( $filteredOrders as $fact ) {
            $testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);

            $testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
            if ( $testStatus && $testDates) {
                $ivas[] = $fact->iva;
            }
        }

        return array_sum($ivas);
    }

    private function getIvaComprasPeriodo() {
        $ivas = array();
        $invoices   = getFromTimOne::getOrdersCxP($this->request->integradoId);

        $filteredOrders = getFromTimOne::filterByDate($invoices, $this->period->startDate->timestamp, $this->period->endDate->timestamp);

        if ( ! empty( $filteredOrders ) ) {
            $respuesta = $this->sumOrders($filteredOrders);
        }
//		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
//		foreach ( $filteredOrders as $fact ) {
//			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);
//
//			$testDates = ($fact->timestamps->createdDate >= $this->period->startDate->timestamp && $fact->timestamps->createdDate <= $this->period->endDate->timestamp);
//			if ( $testStatus && $testDates) {
//				$ivas[] = $fact->iva;
//			}
//		}

        return array_sum($ivas);
    }

    private function getCxP() {
        return $this->pasivo->data = $this->getData($this->txs->odc);
    }

    private function getCxC() {
        return $this->activo->data = $this->getData($this->txs->odv);
    }

    public function getData($Orders){
        $ordenesFiltradas = getFromTimOne::filterOrdersByStatus($Orders,array(5,8,13));
        $sumaOrdenes = getFromTimOne::sumaOrders($ordenesFiltradas);

        return $sumaOrdenes;
    }

    /**
     * @param null $year
     *
     * @return array
     */
    public function setDatesInicioFin( $year  ) {
        $inicio = 'first day of January '.$year;
        if ( isset( $year ) ) {
            $nextYear = (int)$year+1;
            $final = 'first day of January '.$nextYear;
        } else {
            $final = 'first day of this month';
        }

        $timeZone    = new DateTimeZone( 'America/Mexico_City' );
        $fechaInicio = new DateTime( $inicio, $timeZone );
        $fechaFin    = new DateTime( $final, $timeZone );
        $fechaFin->setTime( 0, 0, 0 );
        $fechaInicio->timestamp = $fechaInicio->getTimestamp();
        $fechaFin->timestamp    = $fechaFin->getTimestamp();

        return array ( $fechaInicio, $fechaFin );
    }

    private function setDatesForDisplay() {
        $this->period->startDate   = date('d-m-Y', $this->period->startDate->timestamp);
        $this->period->endDate     = date('d-m-Y', $this->period->endDate->timestamp);
    }

    private function getBancoSaldoEndDate() {
        // TODO: Operar el saldo con las Tx para sacar el saldo a cirre de periodo del balance
        return (float)946;
    }

    private function sumOrders( $orders ) {
        return getFromTimOne::sumaOrders($orders);
    }

    public static function getIntegradoExistingBalanceList($integradoId) {
        $data = getFromTimOne::selectDB('reportes_balance', 'integradoId = '.$integradoId );

        return $data;
    }

    private function getYearFromPeriod() {
        return $this->period->startDate->format('Y');
    }
}

/**
 * @property integer ingresos
 * @property integer egresos
 */
class ReportResultados extends IntegradoOrders {

    protected $fechaInicio;
    protected $fechaFin;
    protected $filtroProyect;

    function __construct( $integradoId, $fechaInicio, $fechaFin, $proyecto=null ) {
        $this->fechaInicio  = $fechaInicio;
        $this->fechaFin     = $fechaFin;
        $this->filtroProyect = $proyecto;

        parent::__construct($integradoId);
    }

    public function getIngresos(){
        $orders = array();

        if($this->filtroProyect != null) {
            foreach ($this->orders->odv as $orden) {
                if ($orden->projectId2 == 0) {
                    if($this->filtroProyect == $orden->projectId){
                        $orders[] = $orden;
                    }
                } else {
                    if($this->filtroProyect == $orden->projectId2 || $orden->projectId == $this->filtroProyect){
                        $orders[] = $orden;
                    }
                }
            }

            $this->orders->odv = $orders;
        }

        $this->orders->odv = getFromTimOne::filterByDate($this->orders->odv, $this->fechaInicio, $this->fechaFin);
        $this->ingresos = $this->getData($this->orders->odv);
    }

    public function getEgresos(){
        $orders = array();

        if($this->filtroProyect != null){
            foreach ($this->orders->odc as $orden) {
                if( !isset($orden->sub_proyecto) ) {
                    if ($orden->proyecto->id_proyecto == $this->filtroProyect) {
                        $orders[] = $orden;
                    }
                }else{
                    if( $orden->sub_proyecto->id_proyecto == $this->filtroProyect){
                        $orders[] = $orden;
                    }
                }
            }
            $this->orders->odc = $orders;
        }

        $this->orders->odc = getFromTimOne::filterByDate($this->orders->odc, $this->fechaInicio, $this->fechaFin);
        $this->egresos = $this->getData($this->orders->odc);
    }

    public function getData($Orders){
        $ordenesFiltradas = getFromTimOne::filterOrdersByStatus($Orders,array(5,8,13));
        $sumaOrdenes = getFromTimOne::sumaOrders($ordenesFiltradas);

        return $sumaOrdenes;
    }

    /**
     * @return mixed
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * @return mixed
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }


}

class ReportFlujo extends IntegradoTxs {

    public $period;
    public $error;
    public $orders;
    public $ingresos;
    public $egresos;
    public $prestamos;
    public $retiros;
    public $depositos;

    function __construct( $integradoId, $fechaInicio, $fechaFin ) {
        $this->setDatesInicioFin($fechaInicio, $fechaFin);

        parent::__construct($integradoId);
        $this->txs = parent::getIntegradoTxs();

        $orders = new IntegradoOrders($integradoId);
        $this->orders = $orders->orders;
    }

    public function getIngresos(){
        $this->orders->odv = $this->filterOrders($this->orders->odv);

        $this->txs->odv = getFromTimOne::filterTxsByDate($this->txs->odv, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);
        $this->ingresos = $this->processTxs($this->txs->odv);
    }

    public function getEgresos(){
        $this->orders->odc = $this->filterOrders($this->orders->odc);

        $this->txs->odc = getFromTimOne::filterTxsByDate($this->txs->odc, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);
        $this->egresos = $this->processTxs($this->txs->odc);
    }

    public function getDepositos(){
        $this->orders->odd = $this->filterOrders($this->orders->odd);

        $this->txs->odd = getFromTimOne::filterTxsByDate($this->txs->odd, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);
        $this->depositos = $this->processTxs($this->txs->odd);
    }

    public function getRetiros(){
        $this->orders->odr = $this->filterOrders($this->orders->odr);

        $this->txs->odr = getFromTimOne::filterTxsByDate($this->txs->odr, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);
        $this->retiros = $this->processTxs($this->txs->odr);
    }

    public function getPrestamos(){
        // TODO: cambiar ($this->orders->odp) por las Txs
//        $this->orders->odp = $this->filterOrders($this->orders->odp);

//        $this->txs->odp = getFromTimOne::filterTxsByDate($this->txs->odp, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);
//        $this->prestamos = $this->processOrders($this->txs->odp);
    }

    private function filterOrders($orders)
    {
        $ordenesFiltradas = getFromTimOne::filterOrdersByStatus($orders,array(5, 8, 13));
        $ordenesFiltradas = getFromTimOne::filterByDate($ordenesFiltradas, $this->period->fechaInicio->timestamp, $this->period->fechaFin->timestamp);

        return $ordenesFiltradas;
    }

    public function processOrders($orders){
        $sumaOrdenes = getFromTimOne::sumaOrders($orders);

        return $sumaOrdenes;
    }

    private function processTxs( $txs ) {
        $sumas = new stdClass();
        $sumas->pagado->neto = 0;
        $sumas->pagado->iva = 0;
        $sumas->pagado->total = 0;

        foreach ( $txs as $key => $value ) {

            switch ($value->orderType) {
                case 'odv':
                    $orden = getFromTimOne::getOrdenesVenta(null, $value->idOrden);
                    $orden = $orden[0];
                    break;
                case 'odc':
                    $orden = getFromTimOne::getOrdenesCompra(null, $value->idOrden);
                    $orden = $orden[0];
                    break;
                case 'odr':
                    $orden = getFromTimOne::getOrdenesRetiro(null, $value->idOrden);
                    $orden = $orden[0];
                    break;
                case 'odd':
                    $orden = getFromTimOne::getOrdenesDeposito(null, $value->idOrden);
                    $orden = $orden[0];
                    break;
                case 'odp':
                    $orden = getFromTimOne::getMutuos(null, $value->idOrden);
                    $orden = $orden[0];
                    break;
            }

            if ( isset( $orden->subTotalAmount ) && isset( $orden->iva ) ) {
                $tasaIva = $orden->iva / $orden->subTotalAmount;
                $ivaTmp = $value->data->amount * $tasaIva;
            } else {
                $ivaTmp = 0;
            }

            // arreglo para suma
            $neto[] = $value->data->amount - $ivaTmp;
            $iva[] = $ivaTmp;
            $total[] = $value->data->amount;

            // asignacion de datos para front
            $value->data->iva = $ivaTmp;
            $value->data->neto = $value->data->amount - $ivaTmp;

            $value->orden->beneficiario = isset($orden->receptor) ? $orden->receptor : null;

            if ( isset( $orden->factura ) ) {
                $value->orden->folio = $orden->factura->comprobante['FOLIO'];
            }
            if ( isset( $orden->proyecto ) ) {
                $value->orden->proyectName = $orden->proyecto->name;
            }
            if ( isset( $orden->sub_proyecto ) ) {
                $value->prden->subProyectName = $orden->sub_proyecto->name;
            }
        }

        if ( isset( $total ) ) {
            $sumas->pagado->neto = array_sum($neto);
            $sumas->pagado->iva = array_sum($iva);
            $sumas->pagado->total = array_sum($total);
        }

        return $sumas;
    }

    /**
     * @param $fechaInicio
     * @param $fechaFin
     */
    public function setDatesInicioFin($fechaInicio, $fechaFin)
    {
        $timeZone = new DateTimeZone('America/Mexico_City');

        if (!isset($fechaInicio)) {
            // Si no viene la fecha, se toma el primero de enero del año en curso
            $fechaInicio = 'first day of January';
            $this->period->fechaInicio = new DateTime($fechaInicio, $timeZone);
        } else {
            $this->period->fechaInicio = DateTime::createFromFormat('d-m-Y H:i:s', $fechaInicio.' 00:00:00', $timeZone);
        }
        if (!isset($fechaFin)) {
            // Si no viene la fecha, se crea con la fecha actual
            $this->period->fechaFin = new DateTime('', $timeZone);
        } else {
            $this->period->fechaFin = DateTime::createFromFormat('d-m-Y H:i:s', $fechaFin.' 23:59:59', $timeZone);
        }
        $this->period->fechaInicio->timestamp = $this->period->fechaInicio->getTimeStamp();
        $this->period->fechaFin->timestamp = $this->period->fechaFin->getTimeStamp();

        try {
            $this->checkDates();
        } catch (Exception $e) {
            $this->error = $e;
        }
    }

    public function checkDates()
    {
        if ($this->period->fechaInicio->timestamp > $this->period->fechaFin->timestamp) {
            throw new Exception(JText::_('ERR_INVALID_DATES'));
        }
    }

}

class IntegradoOrders {

    public $orders;

    function __construct($integradoId){
        self::setOrders($integradoId);
    }

    protected function setTxs( ){
        $this->txs = parent::getIntegradoTxs();
    }
    private function setOrders($integradoId){
        $this->orders->odv = getFromTimOne::getOrdenesVenta($integradoId);
        $this->orders->odc = getFromTimOne::getOrdenesCompra($integradoId);
        $this->orders->odd = getFromTimOne::getOrdenesDeposito($integradoId);
        $this->orders->odr = getFromTimOne::getOrdenesRetiro($integradoId);

        $mutuosOdps = getFromTimOne::getMutuosODP($integradoId);
        if ( ! empty( $mutuosOdps->acreedor ) ) {
            $this->orders->odp_acreedor = $mutuosOdps->acreedor;
        }
        if ( ! empty( $mutuosOdps->deudor ) ) {
            $this->orders->odp_deudor   = $mutuosOdps->deudor;
        }

        $this->getTxs();
    }

    private function getTxs(){
        foreach ($this->orders as $key => $value) {
            foreach ($value as $orden) {
                $orden->txs = getFromTimOne::getTxbyOrderTypeAndOrderId($orden->integradoId, $key, $orden->id);
                $this->getTxDetails($orden->txs);
            }
        }
    }

    /**
     * @param $txs
     */
    public function getTxDetails($txs)
    {
        foreach ($txs as $tx) {
            $tx->detalleTx = getFromTimOne::getTxDataByTxId($tx->idTx);
        }
    }
}

class IntegradoTxs {

    public $txs;
    protected $integradoId;

    function __construct( $integradoId ) {
        $this->integradoId = (int)$integradoId;
    }

    /**
     * @return array
     */
    protected function getIntegradoTxs( ){

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('tm.idIntegrado, tm.idTx, tm.idComision, tm.date, ma.*')
            ->from($db->quoteName('#__txs_timone_mandato', 'tm'))
            ->join('LEFT', $db->quoteName('#__txs_mandatos', 'ma'). ' ON (tm.id = ma.id)' )
            ->where($db->quoteName('tm.idIntegrado') . ' = '. $this->integradoId );
        $db->setQuery($query);
        $results = $db->loadObjectList();

        $this->txs = $this->groupTxByType( $results );

        return $this->txs;
    }

    /**
     * @param $txs
     *
     * @return array
     */
    protected function groupTxByType( $txs ) {
        $txsByType = new stdClass();
        $txsByType->odc = array();
        $txsByType->odv = array();
        $txsByType->odd = array();
        $txsByType->odr = array();
        $txsByType->odp = array();

        foreach ( $txs as $tx ) {
            $txData   = getFromTimOne::getTxDataByTxId( $tx->idTx );
            $tx->data = $txData;

            switch ($tx->orderType) {
                case 'odc':
                    $txsByType->odc[] = $tx;
                    break;
                case 'odv':
                    $txsByType->odv[] = $tx;
                    break;
                case 'odr':
                    $txsByType->odr[] = $tx;
                    break;
                case 'odd':
                    $txsByType->odd[] = $tx;
                    break;
                case 'odp':
                    $txsByType->odp[] = $tx;
                    break;
            }
        }

        return $txsByType;
    }
}


/**
 * UUID class
 *
 * The following class generates VALID RFC 4122 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * @author Andrew Moore
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 */
class UUID
{
    /**
     *
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     */
    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate v5 UUID
     *
     * Version 5 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param	uuid	$namespace
     * @param	string	$name
     */
    public static function v5($namespace, $name)
    {
        if(!self::is_valid($namespace)) return false;

        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2)
        {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        // Calculate hash value
        $hash = sha1($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    public static function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
}

// Usage
// Named-based UUID.

// $v5uuid = UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');

// Pseudo-random UUID

// $v4uuid = UUID::v4();


class Factura extends makeTx {
    public $emisor;
    public $receptor;
    public $datosDeFacturacion;
    public $conceptos;
    public $impuestos;
    public $timbra;
    public $format;

    function __construct( \Integralib\OdVenta $orden, $timbra = false ) {
        $this->emisor = new Emisor($orden->getEmisor());
        $this->receptor = new Receptor($orden->getReceptor());
        $this->datosDeFacturacion = new datosDeFacturacion($orden);

        if(isset ($orden)) {
            $this->setConceptos($orden);
            $this->setImpuestos($orden);
        }
        $this->setTimbra($timbra);
        $this->setFormat();
    }
    /**
     * @param bool $timbra
     */
    public function setTimbra( $timbra ) {
        $this->timbra = isset($timbra) && is_bool($timbra) ? $timbra : false;
    }

    public function setFormat() {
        $this->format = 'Xml';
    }

    public function setTestRFC() {
        $this->emisor->datosFiscales->rfc = 'AAD990814BP7';
        $this->receptor->datosFiscales->rfc = 'AAD990814BP7';
    }

    public function setConceptos($orden) {
        foreach ( $orden->productosData as $key => $concepto ) {
            $this->conceptos[$key] = new \Concepto( $orden->productosData[$key] );
        }
    }

    public function setImpuestos($orden) {

        $impuestos = array();

        $impuestos[0] = $this->getObjectImpuestoFromIVA($orden);
        $ieps = $this->getObjectImpuestoFromIEPS($orden);
        if($ieps->importe != 0){
            $impuestos[1] = $ieps;
        }

        $this->impuestos = $impuestos;
    }

    /**
     * @return \Impuesto
     */
    private function getObjectImpuestoFromIVA($orden) {
        return new \Impuesto($orden->getMontoTotalIVA(), \CatalogoFactory::create()->getFullIva(), 'IVA');
    }

    private function getObjectImpuestoFromIEPS($orden) {
//		TODO: revisar cual es la tasa de IEPS que hay que mandar
        return new \Impuesto($orden->getMontoTotalIEPS(), $orden->productosData[0]->ieps, 'IEPS');
    }

    /**
     * @param $string
     */
    public static function getXmlUUID( $string ) {
        $name = false;

        $parse  = new xml2Array();
        $objXml = $parse->manejaXML( $string );

        if( isset( $objXml->complemento['children'][0]['attrs']['UUID'] )) {
            $name = $objXml->complemento['children'][0]['attrs']['UUID'];
        }

        return $name;
    }

    /**
     * @return bool
     */
    public function sendCreateFactura()
    {
        $this->objEnvio = $this->setObjEnvio();

        $rutas = new servicesRoute();

        $result = parent::create($rutas->getUrlService('facturacion', 'factura', 'create'));

        if ($result === true) {
            $result = $this->returnXML();
        }
        return $result;
    }

    public function returnXML() {
        return $this->resultado->data;
    }

    /**
     * @return $this
     */
    private function setObjEnvio() {
        return $this;

//TODO: quitar mock
//  json_decode('{"emisor":{"datosFiscales":{"rfc":"AAD990814BP7","razonSocial":"Integradora de Emprendimientos Culturales S.A. de C.V.","codigoPostal":"11850","pais":"MEXICO","ciudad":"Ciudad de M\\u00e9xico","delegacion":"Miguel Hidalgo","calle":"Tiburcio Montiel","regime":"1"}},"receptor":{"datosFiscales":{"rfc":"AAD990814BP7","razonSocial":"Integradora de Emprendimientos Culturales S.A. de C.V.","codigoPostal":"11850","pais":"MEXICO","ciudad":"Ciudad de M\\u00e9xico","delegacion":"Miguel Hidalgo","calle":"Tiburcio Montiel","regime":"1"}},"datosDeFacturacion":{"moneda":"MXN","lugarDeExpedicion":"DF","numeroDeCuentaDePago":"DESCONOCIDO","formaDePago":"PAGO EN UNA SOLA EXHIBICION","metodoDePago":"TRANSFERENCIA ELECTRONICA","tipoDeComprobante":"ingreso"},"conceptos":[{"valorUnitario":120,"descripcion":"Producto MochcrearFacturaTest::testCrearFacturaTimone","cantidad":1,"unidad":"pruebas"},{"valorUnitario":120,"descripcion":"Producto MochcrearFacturaTest::testCrearFacturaTimone","cantidad":1,"unidad":"pruebas"}],"format":"Xml"}');
    }

    public function sendCancelFactura(IntegradoSimple $emisor) {
        $this->objEnvio = new stdClass();
        $this->objEnvio->uuid = $this->getXmlUUID($this->xml);
        $this->objEnvio->rfcContribuyente = $emisor->getRfc();

        $rutas = new servicesRoute();

        return parent::create($rutas->getUrlService('facturacion', 'factura', 'cancel'));
    }


}

class Concepto
{

    public $valorUnitario = '100.00';
    public $descripcion = 'Product description';
    public $cantidad = '10';
    public $unidad = 'UNIDAD';

    function __construct( $concepto ) {
        $this->valorUnitario = $concepto->p_unitario;
        $this->descripcion = $concepto->descripcion;
        $this->cantidad = $concepto->cantidad;
        $this->unidad = $concepto->unidad;
    }
}

class Emisor {
    public $datosFiscales;

    function __construct( IntegradoSimple $integrado ) {
        $this->datosFiscales = new datosFiscales( $integrado );
    }
}

class Receptor {
    public $datosFiscales;

    function __construct( IntegradoSimple $integrado ) {
        $this->datosFiscales = new datosFiscales( $integrado );
    }
}

class datosFiscales {
    public $rfc          = 'AAD990814BP7';
    public $razonSocial  = 'Razon Social';
    public $codigoPostal = '03330';
    public $pais         = 'MEXICO';
    public $ciudad       = 'DISTRITO FEDERAL';
    public $delegacion   = 'BENITO JUAREZ';
    public $calle        = 'REFORMA';
    public $regime       = 'PERSONA FISICA';

    function __construct( IntegradoSimple $integ ) {
        $integ = $integ->integrados[0];
        $pJuri = $integ->integrado->pers_juridica;

        $this->rfc          = $pJuri == 2 ? $integ->datos_personales->rfc : $integ->datos_empresa->rfc ;
        $this->razonSocial  = $pJuri == 2 ? $integ->datos_personales->nombre_representante : $integ->datos_empresa->razon_social ;
        $this->regime       = $pJuri ;
        $this->calle        = $pJuri == 2 ? $integ->datos_personales->calle : $integ->datos_empresa->calle ;
        $this->delegacion   = $pJuri == 2 ? $integ->datos_personales->direccion_CP->dMnpio : $integ->datos_empresa->direccion_CP->dMnpio ;
        $this->ciudad       = $pJuri == 2 ? $integ->datos_personales->direccion_CP->dCiudad : $integ->datos_empresa->direccion_CP->dCiudad ;
        $this->codigoPostal = $pJuri == 2 ? $integ->datos_personales->cod_postal : $integ->datos_empresa->cod_postal ;
    }


}

class datosDeFacturacion {
    public $moneda = 'MXN';
    public $lugarDeExpedicion = 'Mexico DF';
    public $numeroDeCuentaDePago = 'DESCONOCIDO';
    public $formaDePago = 'PAGO EN UNA SOLA EXHIBICION';
    public $metodoDePago = 'TRANSFERENCIA ELECTRONICA';
    public $tipoDeComprobante = 'ingreso';

    function __construct( $orden ) {
//        $this->moneda               = $orden->moneda;
        $this->lugarDeExpedicion    = $orden->placeIssue->nombre;
//        $this->numeroDeCuentaDePago = $numeroDeCuentaDePago;
//        $this->formaDePago          = $formaDePago;
//        $this->metodoDePago         = $metodoDePago;
//        $this->tipoDeComprobante    = $tipoDeComprobante;
    }


}

class Impuesto {
    public $importe;
    public $tasa;
    public $impuesto;

    function __construct( $importe, $tasa, $impuesto ) {
        $this->importe  = $importe;
        $this->tasa     = $tasa;
        $this->impuesto = (STRING)$impuesto;
    }


    /**
     * @return mixed
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * @return mixed
     */
    public function getTasa() {
        return $this->tasa;
    }

    /**
     * @return mixed
     */
    public function getImpuesto() {
        return $this->impuesto;
    }

}

class UserTimone {
    public $uuid  = '';
    public $name  = '';
    public $email = '';

    function __construct( Integrado $integrado ) {
        $user = $integrado->getUsuarioPrincipal($integrado->integrados[0]->integrado->integrado_id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->uuid = $integrado->integrados[0]->integrado->integrado_id;
    }
}

class Cashout extends makeTx{
    protected $objEnvio;

    /**
     * @param $orden
     * @param $idPagador
     * @param $idBeneficiario
     * @param $totalAmount
     * @param $options array(accountId => (INT), paymentMethod => (INT) )
     */
    function __construct($orden, $idPagador, $idBeneficiario, $totalAmount, $options)
    {
        $this->options  = $options;
        $this->orden    = $orden;

        $this->objEnvio->amount   = (FLOAT)$totalAmount;
        $this->objEnvio->uuid     = parent::getTimOneUuid($idPagador);
        $this->setDataBeneficiario($idBeneficiario, $options['accountId']);
    }

    private function setDataBeneficiario( $idBenefiario, $accountId){
        $beneficiario = new IntegradoSimple($idBenefiario);

        foreach ( $beneficiario->integrados[0]->datos_bancarios as $banco ) {
            if($accountId == $banco->datosBan_id){
                $this->objEnvio->clabe    = $banco->banco_clabe;
                $this->objEnvio->bankCode = (INT)$banco->banco_codigo;
            }
        }
    }

    public function getComisionFijaTx() {
        switch($this->options['paymentMethod']) {
            case 0: // SPEI
                $comision = $this->comisionSPEI();
                break;
            case 1: // Cheque
                $comision = $this->comsionCheque();
        }

        return $comision;
    }

    private function comisionSPEI() {
        // TODO: crear el parametro y traerlo de la db
        $neto = 8; // siempre aplica IVA 16

        return $neto;
    }

    private function comsionCheque() {
        // TODO: crear el parametro y traerlo de la db
        $neto = 15; // siempre aplica IVA 16

        return $neto;
    }

    public function sendCreateTx() {
        $rutas = new servicesRoute();
        $result = parent::create($rutas->getUrlService('timone', 'cashOut', 'create'));

        if ( $result === true ) {
            $this->saveTxOrderRelationship();
        }

        return $result;
    }
}

class transferFunds extends makeTx {
    protected $objEnvio;

    function __construct($orden, $idPagador, $idBeneficiario, $totalAmount){
	    $this->orden    = $orden;

	    $this->objEnvio->uuidOrigin       = parent::getTimOneUuid($idPagador);
	    $this->objEnvio->uuidDestination  = parent::getTimOneUuid($idBeneficiario);
	    $this->objEnvio->amount           = (float)$totalAmount;
    }

    public function sendCreateTx()
    {
        $rutas = new servicesRoute();
        $result = parent::create($rutas->getUrlService('timone', 'transferFunds', 'create'));

        if ( $result === true ) {
            $this->saveTxOrderRelationship();
        }

        return $result;
    }

}

/**
 * @property  object resultado
 */
class makeTx {
    protected $objEnvio;
    protected $resultado;

    protected function create($datosEnvio){
        unset($this->options);

        $request = new sendToTimOne();
        $request->setServiceUrl($datosEnvio->url);
        $request->setJsonData(json_encode($this->objEnvio));
        $request->setHttpType($datosEnvio->type);

        $this->resultado = $request->to_timone();

        jimport('joomla.log.log');

        JLog::addLogger(array('text_file' => date('Y-m-d').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora');
        $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->objEnvio, $request) ) ) );
        JLog::add($logdata, JLog::DEBUG, 'bitacora');

        return $this->resultado->code == 200;
    }

    public function saveTxOrderRelationship() {
        $db = JFactory::getDbo();
        $request = new sendToTimOne();
        $comisionesOfIntegrado = getFromTimOne::getComisionesOfIntegrado($this->orden->integradoId);
        $comisionAplicable = getFromTimOne::getAplicableComision($this->orden->orderType,$comisionesOfIntegrado);

        $txsTimoneMandatoObj = new stdClass();
        $txsTimoneMandatoObj->idTx = $this->resultado->data;
        $txsTimoneMandatoObj->idIntegrado = $this->orden->integradoId;
        $txsTimoneMandatoObj->date = time();
        $txsTimoneMandatoObj->idComision = $comisionAplicable->id;

        $db->transactionStart();

        try{
            $db->insertObject('#__txs_timone_mandato',$txsTimoneMandatoObj);

            $txsMandatosRel = new stdClass();

            $txsMandatosRel->id = $db->insertid();
            $txsMandatosRel->amount = $this->objEnvio->amount;
            $txsMandatosRel->orderType = $this->orden->orderType;
            $txsMandatosRel->idOrden = $this->orden->id;

            $db->insertObject('#__txs_mandatos',$txsMandatosRel);

            $resultado = true;

            $db->transactionCommit();
        }catch (Exception $e){
            $db->transactionRollback();
            $resultado = false;
        }

        JLog::addLogger(array('text_file' => date('d-m-Y').'_bitacora_makeTxs.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora');
        $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($request, $resultado) ) ) );
        JLog::add($logdata, JLog::DEBUG, 'bitacora_txs');
    }

    protected function getTimOneUuid($idIntegradoEnvia){
        $integradoEnvia = new IntegradoSimple($idIntegradoEnvia);
        $integradoEnvia->getTimOneData();

        return $integradoEnvia->timoneData->timoneUuid;
    }


}

class OrdenFn {

    protected $minAmount;
    protected $order;

    public static function getStatusIdByName( $string ) {
        $statusCatalog = getFromTimOne::getOrderStatusCatalogByName();

        return $statusCatalog[ ucfirst(strtolower($string)) ]->id;
    }

    public static function getMinAmount() {
        return 0;
    }

    public static function getCantidadAutRequeridas(IntegradoSimple $emisor, IntegradoSimple $receptor){
        $auth = 0;
        $cant_auths = new stdClass();

        if( $emisor->isIntegrado() ){
            $cant_auths->emisor = $auth+$emisor->getOrdersAtuhorizationParams();
        }
        if( $receptor->isIntegrado() ){
            $cant_auths->receptor = $auth+$receptor->getOrdersAtuhorizationParams();
        }

        $cant_auths->totales = array_sum((array)$cant_auths);

        return $cant_auths;
    }

    public static function getIdEmisor($order, $orderType) {
        switch ($orderType){
            case 'odv':
                $return = $order->integradoId;
                break;
            case 'odc':
                $return = $order->integradoId;
                break;
            case 'odd':
                $return = $order->integradoId;
                break;
            case 'odr':
                $return = null;
                break;
            case 'odp':
                $return = $order->integradoIdA;
                break;
            case 'mutuo':
                $return = $order->integradoIdE;
                break;
            case 'odp':
                $return = $order->acreedor;
                break;
        }

        return $return;
    }

    public static function getIdReceptor($order, $orderType) {
        switch ($orderType){
            case 'odv':
                $return = $order->clientId;
                break;
            case 'odc':
                $return = $order->proveedor;
                break;
            case 'odd':
                $return = null;
                break;
            case 'odr':
                $return = $order->integradoId;
                break;
            case 'odp':
                $return = $order->integradoIdD;
                break;
            case 'mutuo':
                $return = $order->integradoIdR;
                break;
            case 'odp':
                $return = $order->deudor;
                break;
        }

        return $return;
    }

	public static function getRelatedOdvIdFromOdcId( $id_odc ) {
		$result = getFromTimOne::selectDB('ordenes_odv_odc_relation', 'id_odc = '.(INT)$id_odc);
		return $result[0]->id_odv;
	}

	public function calculateBalance( $order ) {
        $this->order = $order;

        $this->order->sumOrderTxs = 0;
        $this->order->txs = $this->getOrderTxs();
        if ( !empty( $this->order->txs ) ) {
            foreach ( $this->order->txs as $tx ) {
                $this->order->sumOrderTxs = $this->order->sumOrderTxs + $tx->totalAmountTxs;
            }
        }

        return $this->order->totalAmount - $this->order->sumOrderTxs;
    }

    private function getOrderTxs() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('txs.id, txs.idTx, txs.idIntegrado, txs.date, txs.idComision, SUM(piv.amount) AS totalAmountTxs, piv.idOrden, piv.orderType')
            ->from($db->quoteName('#__txs_timone_mandato', 'txs') )
            ->join('left', $db->quoteName('#__txs_mandatos', 'piv') . ' ON ( txs.id = piv.id )' )
            ->where('piv.idOrden = '.$db->quote($this->order->id).' AND piv.orderType = '.$db->quote($this->order->orderType));
        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}