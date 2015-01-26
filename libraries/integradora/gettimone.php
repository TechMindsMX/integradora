<?php
defined('JPATH_PLATFORM') or die;

define('XML_FILES_PATH', JPATH_BASE.'/media/facturas/');

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.rutas');
jimport('integradora.xmlparser');
jimport('integradora.integrado');

class getFromTimOne{
    public static function getOrdenAuths($idOrden, $tipo){
        $tabla = sendToTimOne::getTableByType($tipo);

        $authorizations = self::selectDB($tabla,'idOrden = '.$idOrden);

        if (isset($authorizations)) {
            foreach($authorizations as $key => $value){
                $value->idOrden  = (INT)$value->idOrden;
                $value->userId   = (INT)$value->userId;
                $value->authDate = (STRING)$value->authDate;
            }
        }

        return $authorizations;
    }

    public static function checkUserAuth($auths){
        $userId = JFactory::getUser()->id;
        $userAsAuth = false;

        foreach ($auths as $auth) {
            if($auth->userId === (INT)$userId) {
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

    // TODO: Este metodo se elimina y se piden las Txs a TimOne
    public static function getTxIntegradoSinMandato($integradoId=null, $idTX = null)
    {
        $where = null;
        if (!is_null($idTX)) {
            $where = 'id = ' . $idTX;
        } elseif (!is_null($integradoId)) {
            $where = 'integradoId = ' . $integradoId;
        }

        $txs = self::getTxConciliaciones($where);

        return $txs;
    }

    // TODO: Este metodo se elimina, sirve como modelo del objeto de datos
    public static function getTxConciliaciones($where){

        $txs = self::selectDB('txs_banco_integrado',$where,'','getFromTimOne');

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

        $tabla= new stdClass();
        $tabla->intereses_con_iva      = $data->interes*1.16;
        $tabla->capital       = $data->capital;
        $tabla->tipoPeriodos  = $data->tiempoplazo;
        switch($data->tipoPlazo){
            case 1:
                $tabla->tperiodo        = 'Diaria';
                $tabla->periodos_year   = '365';
                break;
            case 2:
                $tabla->tperiodo         = 'Quincenal';
                $tabla->periodos_year    = '104';
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
        $temp           = (float) $temp/$data->tiempoplazo;
        $temp           = (float) $temp+1;
        $temp           = (float) pow($temp, $data->tiempoplazo);
        $temp           = (float) $temp-1;
        $temp           = (float) $temp*$data->tiempoplazo;

        $tabla->tasa_periodo           = (float) $tabla->intereses_con_iva;
        $tabla->tasa_efectiva_periodo  = (float) $temp*100;
        $tabla->capital_fija           = (float) $data->capital/$data->tiempoplazo;
        $final                         = (float) $tabla->capital;
        $capital                       = (float) $tabla->capital_fija;

        for($i = 1; $i <= $data->tiempoplazo; $i++ ){
            $inicial              = (float)$final;
            $intiva               = (float)$inicial*($tabla->intereses_con_iva/100);

            $intereses            = (float)$intiva/1.16;
            $iva                  = (float)$intereses*0.16;
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
        $temp                           = (float) pow($temp ,$data->tiempoplazo);
        $number1                        = (float) $temp*($tabla->intereses_con_iva/100);
        $number2                        = (float) $temp-1;
        $tabla->factor                  = (float) $number1/$number2;
        $tabla->cuota_Fija              = (float) $tabla->factor*$tabla->capital;
        $saldo_final                    = (float) $tabla->capital;

        for($i = 1; $i <= $data->tiempoplazo; $i++ ){
            $saldo_inicial                    = (float)$saldo_final;
            $intiva                           = (float)$saldo_inicial*($tabla->intereses_con_iva/100);
            $intereses                        = (float)$intiva/1.16;
            $iva                              = (float)$intereses*0.16;
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

        $tipos = getFromTimOne::getTiposPago();
        $integradoAcredor = new stdClass();
        $integradoDeudor  = new stdClass();


        foreach ($mutuos as $key => $data) {
            $integradoEmisor = new IntegradoSimple($data->integradoIdE);
            $integradoReceptor = new IntegradoSimple($data->integradoIdR);

            if (is_null($integradoEmisor->integrados[0]->datos_empresa)) {
                $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_personales->nom_comercial;
                $integradoAcredor->rfc = $integradoEmisor->integrados[0]->datos_personales->rfc;
            } else {
                $integradoAcredor->nombre = $integradoEmisor->integrados[0]->datos_empresa->razon_social;
                $integradoAcredor->rfc = $integradoEmisor->integrados[0]->datos_empresa->rfc;
            }

            if (is_null($integradoReceptor->integrados[0]->datos_empresa)) {
                if (is_null($integradoReceptor->integrados[0]->datos_personales->nom_comercial)) {
                    $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nombre_representante;
                    $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_personales->rfc;
                } else {
                    $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_personales->nom_comercial;
                    $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_personales->rfc;
                }
            } else {
                $integradoDeudor->nombre = $integradoReceptor->integrados[0]->datos_empresa->razon_social;
                $integradoDeudor->rfc = $integradoReceptor->integrados[0]->datos_empresa->rfc;
            }

            $integradoAcredor->datosBancarios = $integradoEmisor->integrados[0]->datos_bancarios;
            $integradoDeudor->datosBancarios = $integradoReceptor->integrados[0]->datos_bancarios;

            $data->integradoAcredor = $integradoAcredor;
            $data->integradoDeudor = $integradoDeudor;
        }

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
        $data = self::selectDB('integrado_timone','timOneId = '.$timOneId);

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
                $val->data->date = getFromTimOne::convertDateLength($val->data->date, 10);
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
    public static function getTxbyIntegradoByOrderTypeAndId($integradoId, $orderType, $idOrden){
        return self::selectDB('txs_timone_mandato', 'idIntegrado = '.$integradoId.' AND tipoOrden = "'.$orderType.'" AND idOrden = '.$idOrden);
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
            $orden->acreedor          = (STRING)$value->acreedor;
            $orden->a_rfc             = (STRING)$value->a_rfc;
            $orden->deudor            = (STRING)$value->deudor;
            $orden->d_rfc             = (STRING)$value->d_rfc;
            $orden->capital           = (FLOAT)$value->capital;
            $orden->intereses         = (FLOAT)$value->intereses;
            $orden->iva_intereses     = (FLOAT)$value->iva_intereses;
            $orden->status            = (INT)$value->status;

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

        if(!is_null($integradoId) && !is_null($projectId)){
            $where = 'id_proyecto = '.$projectId;
        }elseif(!is_null($integradoId) && is_null($projectId)){
            $where = 'integradoId = '.$integradoId;
        }elseif(!is_null($projectId) && is_null($integradoId)){
            $where = 'id_proyecto = '.$projectId;
        }
        $respuesta = self::selectDB('integrado_proyectos',$where,'id_proyecto');

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
            $query->select('id AS client_id, integradoIdCliente AS id, tipo_alta AS type, integrado_id AS integrado_id, status, bancos AS bancoIds')
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

                $querygral->select('DE.rfc, DP.nom_comercial AS tradeName, DE.razon_social AS corporateName, DP.nombre_representante AS contact')
                          ->from('#__integrado_datos_personales AS DP')
                          ->join('INNER', $db->quoteName('#__integrado_datos_empresa', 'DE') . ' ON (' . $db->quoteName('DE.integrado_id') . ' = ' . $db->quoteName('DP.integrado_id') . ')')
                          ->where('DE.integrado_id = ' . $value->id);
                try {
                    $db->setQuery($querygral);
                    $general = $db->loadObject();

                    $value->rfc = @$general->rfc;
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
                $db = JFactory::getDbo();
                $querybanco = $db->getQuery(true);

                $bancoIds = isset($value->bancoIds) ? ' IN '.json_decode($value) : '';

                $querybanco->select('*')
                           ->from('#__integrado_datos_bancarios')
                           ->where('integrado_id = ' . $value->id );

                try {
                    $db->setQuery($querybanco);
                    $banco = $db->loadObjectList();
                    $value->bancos = $banco;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

            foreach ($response as $key => $value) {
                foreach ($value->bancos as $indice => $valor) {
                    $valor->banco_cuenta_xxx = 'XXXXXX' . substr($valor->banco_cuenta, -4, 4);
                    $valor->banco_clabe_xxx = 'XXXXXXXXXXXXXX' . substr($valor->banco_clabe, -4, 4);
                }
            }
        }else{
            //Se regresan los datos de los clientes/proveedores dados de alta.
            $query->select('clientes.id AS client_id, clientes.integradoIdCliente AS idCliPro, clientes.integrado_Id AS integradoId, clientes.tipo_alta, clientes.monto, clientes.status,
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

        if($type == 0 && !empty($response)){
            foreach ($response as $value) {
                if($value->type == $type){
                    $clientes[] = $value;
                }
            }
            $response = $clientes;
        }elseif($type == 1 && !empty($response)){
            foreach ($response as $value) {
                if($value->type == $type){
                    $proveedores[] = $value;
                }
            }
            $response = $proveedores;
        }

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
            $receptor    = new IntegradoSimple($value->integradoId);
            $value->recepror        = $receptor->getDisplayName();

            $value->status = self::getOrderStatusName($value->status);
            $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);

            // TODO: Cambiar por metodo que busca los pagos asociados a la orden
            $value->partialPaymentsTotal = 350.21;
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
            $value->status          = self::getOrderStatusName($value->status);
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
            $value->cuentaId        = 0;

            $integCurrent = new IntegradoSimple(JFactory::getSession()->get('integradoId',null,'integrado'));
            $value->cuenta = $integCurrent->integrados[0]->datos_bancarios[$value->cuentaId];
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
            $value->status          = (INT)$value->status;
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
            $value->urlXML          = (STRING)$value->urlXML;
            $value->observaciones   = (STRING)$value->observaciones;

            $value = self::getProyectFromId($value);
            $value = self::getClientFromID($value);
            $value->status = self::getOrderStatusName($value->status);

            $xmlFileData            = file_get_contents(JPATH_BASE.DIRECTORY_SEPARATOR.$value->urlXML);
            $data 			        = new xml2Array();
            $value->factura         = $data->manejaXML($xmlFileData);

            $value->subTotalAmount  = (float)$value->factura->comprobante['SUBTOTAL'];
            $value->totalAmount     = $value->factura->comprobante['TOTAL'];
            $value->iva             = $value->factura->impuestos->iva->importe;
            $value->ieps            = $value->factura->impuestos->ieps->importe;
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

            $value = self::getProyectFromId($value);
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
        $payMethod = new stdClass();
        $names = array('SPEI','Cheque','Pago en taquilla','algo','otro');

        $payMethod->id = $paymentMethodId;
        $payMethod->name = $names[$paymentMethodId];

        return $payMethod;
    }

    public static function getProyectFromId($orden){
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

        foreach ($clientes as $key => $value) {
            if ( $value->type == 0 ) {
                $proveedores[ $value->id ] = $value;
            } elseif ( $value->type == 1 ) {
                $proveedores[ $value->id ] = $value;
            }
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
            if($orden->status === 4){
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

    public static function getSaldoOperacionesPorLiquidar($integardoId){
        $allOdv = self::getOperacionesPorLiquidar($integardoId);
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
            ->from($db->quoteName('#__ordenes_venta', 'a'))
            ->join('INNER', $db->quoteName('#__facturasxcobrar', 'b') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.id_odv') . ')')
            ->where($db->quoteName('a.status') . ' =28');
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
            $transaction->data = getFromTimOne::getTxDataByTxId($transaction->id);
        }

        return $txs;
    }

    public static function getTxDataByTxId($txId) {

        // TODO: traer los datos de la Tx desde TimOne

        $intId = JFactory::getSession()->get('integradoId', null, 'integrado');
        $where = JFactory::getDbo()->quoteName('idIntegrado') . ' = '. $intId;
        $results = getFromTimOne::selectDB('txs_timone_mandato', $where, 'idTx');

        foreach ( $results as $key => $val ) {
            $txstp = new stdClass;
            $txstp->referencia = 'A458455A554SJHS445AA2'.$key;
            $txstp->integradoId = $results[$txId]->idIntegrado;
            $txstp->date = $results[$txId]->date;
            $txstp->amount = 10.10 * ($key+1);

            $array[$key] = $txstp;
        }

        $txId = (int)$txId;

        return @$array[$txId];
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
                $objeto->timestamps->$key = (INT)$value;
            }
        }

    }

    public static function token(){
        $token = 'fghgjsdatr';
        return $token;
    }

    public static function newintegradoId($envio){
        $db		= JFactory::getDbo();
        $query 	= $db->getQuery(true);

        $query->insert($db->quoteName('#__integrado'))
              ->columns($db->quoteName('status').','.$db->quoteName('pers_juridica'))
              ->values($db->quote(0).','.$db->quote($envio));

        $db->setQuery($query);
        $db->execute();
        $newId = $db->insertid();

        return $newId;
    }

    public static function getTxSTPbyRef( $id ) {
        $txs = getFromTimOne::selectDB( 'txs_timone_mandato', 'id = '.(int)$id );

        foreach ( $txs as $transaction ) {
            $transaction->data = getFromTimOne::getTxDataByTxId($transaction->id);
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

    public static function getFacturasVenta($integradoId)
    {
        return self::getOrdenesVenta($integradoId);
    }

    public static function getCurrencies(){
        $currencies = self::selectDB('catalog_currencies',null);

        return $currencies;
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
            $comisiones[] = $result[0];
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

        switch ($tipoOrden) {
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

        if ( ! empty( $comisiones ) && isset($triggerSearch) ) {
            foreach ( $comisiones as $key => $com ) {
                if($com->trigger == $triggerSearch) {
                    $comision = $com;
                }
            }
        }

        // TODO: verificar $orden->totalAmount con el comprobante del xml
        $montoComision = isset($comision) ? $orden->totalAmount * ($comision->rate / 100) : null;

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

    function __construct () {
        $this->serviceUrl   = null;
        $this->jsonData     = null;
        $this->setHttpType('GET');
    }

    public static function uploadFiles() {
        $save = array();
        $db 	= JFactory::getDbo();

        $data	= JFactory::getApplication()->input->getArray();
        if ( isset( $data['integradoId'] ) ) {
            $integrado_id = $data['integradoId'] != '' ? $data['integradoId'] : '';
        } else {
            $integrado_id = '';
        }

        foreach ($_FILES as $key => $value) {
            manejoImagenes::cargar_imagen($value['type'], $integrado_id, $value, $key);
            $columna 	= substr($key, 3);
            $clave 		= substr($key, 0,3);
            $where		= $db->quoteName('integrado_id').' = '.$integrado_id;

            switch ($clave) {
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
                    $where = $db->quoteName('integrado_id').' = '.$integrado_id.' AND '.$db->quoteName('instrum_type').' = 1';
                    break;
                case 't2_':
                    $table = 'integrado_instrumentos';
                    $where = $db->quoteName('integrado_id').' = '.$integrado_id.' AND '.$db->quoteName('instrum_type').' = 2';
                    break;
                case 'pn_':
                    $table = 'integrado_instrumentos';
                    $where = $db->quoteName('integrado_id').' = '.$integrado_id.' AND '.$db->quoteName('instrum_type').' = 3';
                    break;
                case 'rp_':
                    $table = 'integrado_instrumentos';
                    $where = $db->quoteName('integrado_id').' = '.$integrado_id.' AND '.$db->quoteName('instrum_type').' = 4';
                    break;

                default:

                    break;
            }
            $updateSet 	= array($db->quoteName($columna).' = '.$db->quote("media/archivosJoomla/" . $integrado_id.'_'.$key . ".jpg") );
            $save[] = self::updateData($table, $updateSet, $where);
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

        $respuesta = is_null($resultado->lastOrderNum)?1:$resultado->lastOrderNum+1;

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

    /* DATA PARA GUARDADO DE ORDEN DE PRESTAMO
     * $data = new stdClass();
     * $data->fecha_elaboracion=time();
     * $data->fecha_deposito=time();
     * $data->tasa=3;
     * $data->tipo_movimiento='prestamo';
     * $data->acreedor='1';
     * $data->a_rfc='AUEN120101GA1';
     * $data->deudor='2';
     * $data->d_rfc='BAEM120101FE3';
     * $data->capital=1230;
     * $data->intereses=123;
     * $data->iva_intereses=23;
     * RETORNA ID DE PRESTAMO GUARDADO
     */
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
            echo '<pre>'.$e->getMessage().'</pre>';
            $return = false;
        }

        if( ($last_inserted_id) && ($return)){
            $return= $db->insertid();
        }

        return $return;
    }

    public function updateDB($table, $set=null, $condicion=null){
        $set = is_null($set)?$this->set:$set;

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
            echo '<pre>'.$e->getMessage().'</pre>';
            $return = false;
        }

        return $return;
    }

    public function deleteDB($table, $condicion){
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
        $verbose = fopen('curl.log', 'a+');
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
        $integrado = new IntegradoSimple($integradoId);

        $order = getFromTimOne::getOrdenes($integradoId, $idOrder, self::getTableByType($orderType));
        $order = $order[0];

        //simulado
        $integrado->cantidadAuthNecesarias = $integrado->getOrdersAtuhorizationParams();

        $tableAuth = $orderType.'_auth';
        $order->auths = getFromTimOne::getOrdenAuths($order->id, $tableAuth);

        $order->hasAllAuths = $integrado->cantidadAuthNecesarias == count($order->auths);
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
                $return = in_array($orderNewStatus, array(3,5)) && $order->hasAllAuths;
                break;
            case 3:
                $return = in_array($orderNewStatus, array(5,55)) && $order->hasAllAuths;
                break;
            case 5:
                if($orderNewStatus < $order->status){
                    $return = $orderNewStatus == 3 && $order->hasAllAuths;
                }else {
                    $return = $orderNewStatus == 8 && $order->hasAllAuths;
                }
                break;
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

    public function sendSolicitudLiquidacionTIMONE($monto, $integradoId){
        //TODO: metodo en el que se va a enviar los datos a TIMONE para que registre la transacción y no debería regresar el id de esta.
    }

    public function generarFacturaComisiones($dataFactura){
        // TODO: crear las facturas de comisiones
    }

    public function generaObjetoFactura( $newOrden ) {

        $emisor = new Emisor( $newOrden->integradoId );
        $receptor = new Receptor( $newOrden->proveedor->id );
        $datosDeFacturacion = new datosDeFacturacion( $newOrden );

        foreach ( $newOrden->productosData as $key => $concepto ) {
            $conceptos[$key] = new Conceptos( $newOrden->productosData[$key] );
        }

        $data = new Factura( $emisor, $receptor, $datosDeFacturacion, $conceptos);

        return $data;
    }

    public function generateFacturaFromTimone( $factura ) {
        // TODO: quitar mocks de sandbox
//mocks sandbox
        $serviceUrl = 'http://api.timone-factura.mx/facturacion/create';
        $rfcTest = 'AAD990814BP7';
        $factura->emisor->datosFiscales->rfc = $rfcTest;
        $factura->receptor->datosFiscales->rfc = $rfcTest;
//fin mocks sandbox

        $jsonData   = json_encode( $factura );
        $httpType   = 'POST';

        $request = new sendToTimOne();
        $request->setServiceUrl( $serviceUrl );
        $request->setJsonData( $jsonData );
        $request->setHttpType( $httpType );

        $result = $request->to_timone(); // realiza el envio

        return $result->data;
    }

    /**
     * @param $data
     *
     * @return bool|string filename {uuid}.xml
     */
    public function saveXMLFile( $data ) {
        $xmlpath = XML_FILES_PATH;
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

            switch ($value->tipoOrden) {
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

            $value->orden->beneficiario = isset($orden->receptor) ? $orden->proveedor->corporateName : null;

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
                $orden->txs = getFromTimOne::getTxbyIntegradoByOrderTypeAndId($orden->integradoId, $key, $orden->id);
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

        $where = JFactory::getDbo()->quoteName('idIntegrado') . ' = '. $this->integradoId;
        $results = getFromTimOne::selectDB('txs_timone_mandato', $where);

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

            switch ($tx->tipoOrden) {
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


class Factura {
    public $emisor;
    public $receptor;
    public $datosDeFacturacion;
    public $conceptos;
    public $format;

    function __construct(Emisor $emisor, Receptor $receptor, datosDeFacturacion $datosDeFacturacion, $conceptos) {
        $this->emisor = $emisor;
        $this->receptor = $receptor;
        $this->datosDeFacturacion = $datosDeFacturacion;
        $this->conceptos = $conceptos;
        $this->setFormat();
    }

    public function setFormat() {
        $this->format = 'Xml';
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
}

class Conceptos
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

    function __construct( $integradoId ) {
        $integ = new IntegradoSimple($integradoId);
        $this->datosFiscales = new datosFiscales( $integ );
    }
}

class Receptor {
    public $datosFiscales;

    function __construct( $integradoId ) {
        $integ = new IntegradoSimple($integradoId);
        $this->datosFiscales = new datosFiscales( $integ );
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

class Cashout {

    function __construct($ordenRetiro)
    {
        $this->clabe = $ordenRetiro->cuenta->banco_clabe;
        $this->bankCode = (INT)$ordenRetiro->cuenta->banco_codigo;
        $this->amount = (FLOAT)$ordenRetiro->totalAmount;
        $this->uuid = $this->getUuid($ordenRetiro);

    }

    private function getUuid($orden)
    {
        $integrado = new IntegradoSimple($orden->integradoId);
        $timoneUUID = $integrado->timonedata->timoneUuid;

        return $timoneUUID->timoneUuid;
    }


}