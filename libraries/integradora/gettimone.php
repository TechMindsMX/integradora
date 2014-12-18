<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.rutas');
jimport('integradora.xmlparser');

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

    public static function getTabla($data){

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
        $capital                       = (float) round($tabla->capital_fija);

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
                'acapital'      => (float) round($tabla->capital_fija),
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
            $saldo_inicial                    = (float)round($saldo_final);
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

    public static function getMutuos($integradoId=null, $idMutuo=null){
        $where = null;
        if(is_null($integradoId)){
            $where = 'id = '.$idMutuo;
        }elseif(is_null($idMutuo)){
            $where = 'integradoIdE = '.$integradoId;
        }
        $mutuos = self::selectDB('mandatos_mutuos',$where,'');

        return $mutuos;
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
        $data = self::selectDB('integrado_timOne','timOneId = '.$timOneId);

        return $data;
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
        $respuesta = self::selectDB('integrado_proyectos',$where);

        return $respuesta;
    }

    public static function getProducts($integradoId = null, $productId = null){
        $where = null;

        if(is_null($integradoId) && is_null($productId)){
            $where = null;
        }elseif(!is_null($integradoId) && is_null($productId)){
            $where = 'integradoId = '.$integradoId;
        }elseif(!is_null($productId) && is_null($integradoId)){
            $where = 'id_producto = '.$productId;
        }
        $respuesta = self::selectDB('integrado_products',$where);

        return $respuesta;
    }

    public static function getClientes($userId = null, $type = 2){
        $db       = JFactory::getDbo();
        $query    = $db->getQuery(true);

        if( !is_null($userId) ) {
            //Obtiene todos los id de los clientes/proveedores dados de alta para un integrado
            $query->select('integradoIdCliente AS id, tipo_alta AS type, integrado_id AS integrado_id, status')
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

                $querybanco->select('*')
                    ->from('#__integrado_datos_bancarios')
                    ->where('integrado_id = ' . $value->id);

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
            $query->select('clientes.integradoIdCliente AS idCliPro, clientes.integrado_Id AS integradoId, clientes.tipo_alta, clientes.monto, clientes.status,
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
	    $orders->odr = self::getOrdenesRetiro($intergradoId);
	    $orders->odc = self::getOrdenesCompra($intergradoId);

        if ( ! empty( $orders ) ) {
            foreach ( $orders as $key => $values ) {
                $orders->$key = self::filterOrdersByStatus($values, array(1,3,5,8));
            }
        }

        return $orders;
    }

    public static function getOrdersCxC( $intergradoId = null ){
        $orders = new stdClass();
	    $orders->odd = self::getOrdenesDeposito($intergradoId);
	    $orders->odv = self::getOrdenesVenta($intergradoId);

        if ( ! empty( $orders ) ) {
            foreach ( $orders as $key => $values ) {
                $orders->$key = self::filterOrdersByStatus($values, array(1,3,5,8));
            }
        }

        return $orders;
    }

    /**
     * @param $orders array
     * @param $statusId array
     */
    private static function filterOrdersByStatus( $orders, $statusId ){
        $resultados = array();

        foreach ( $orders as $key => $value ) {
            if (in_array($value->status->id, $statusId)) {
                $resultados[] = $value;
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
			$value->status          = (INT)$value->status;
			$value->totalAmount     = (FLOAT)$value->totalAmount;
			$value->createdDate     = (STRING)$value->createdDate;
			$value->paymentDate     = (STRING)$value->paymentDate;
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

			$xmlFileData            = file_get_contents($value->urlXML);
			$data 			        = new xml2Array();
			$value->factura         = $data->manejaXML($xmlFileData);

			$value->subTotalAmount  = $value->factura->comprobante['SUBTOTAL'];
			$value->totalAmount     = $value->factura->comprobante['TOTAL'];
			$value->iva             = $value->factura->impuestos->iva->importe;
			$value->ieps            = $value->factura->impuestos->ieps->importe;
		}


		return $orden;
	}

	public static function getOrdenesVenta($integradoId = null, $idOrden = null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_venta');

        //Cambio el tipo de dato para las validaciones con (===)
        foreach ($orden as $key => $value) {
            $value->id             = (INT)$value->id;
            $value->integradoId    = (INT)$value->integradoId;
	        $value->orderType      = 'odv';
            $value->numOrden       = (INT)$value->numOrden;
            $value->projectId      = (INT)$value->projectId;
            $value->projectId2     = (INT)$value->projectId2;
            $value->clientId       = (INT)$value->clientId;
            $value->account        = (INT)$value->account;
	        $value->paymentMethod   = self::getPaymentMethodName($value->paymentMethod);
            $value->conditions     = (INT)$value->conditions;
            $value->placeIssue     = (INT)$value->placeIssue;
            $value->status         = (INT)$value->status;
            $value->productos      = (STRING)$value->productos;
            $value->createdDate    = (STRING)$value->createdDate;
            $value->paymentDate    = (STRING)$value->paymentDate;

	        $subTotalOrden        = 0;
	        $subTotalIva          = 0;
	        $subTotalIeps         = 0;

	        $value->productosData = json_decode($value->productos);

	        foreach ($value->productosData  as $producto ) {
		        $subTotalOrden  = $subTotalOrden + $producto->cantidad * $producto->p_unitario;
		        $subTotalIva    = $subTotalIva + ($producto->cantidad * $producto->p_unitario) * ($producto->iva/100);
		        $subTotalIeps   = $subTotalIeps + ($producto->cantidad * $producto->p_unitario) * ($producto->ieps/100);
	        }

	        $value->subTotalAmount = $subTotalOrden;
	        $value->totalAmount    = $subTotalOrden + $subTotalIva + $subTotalIeps;
	        $value->iva      = $subTotalIva;
	        $value->ieps     = $subTotalIeps;

	        $value = self::getProyectFromId($value);
	        $value = self::getClientFromID($value);
	        $value->status = self::getOrderStatusName($value->status);

	        // TODO: Cambiar por metodo que busca los pagos asociados a la orden
	        $value->partialPaymentsTotal = 350.21;
        }

        return $orden;
    }

	public static function getOrderStatusCatalog( ){
		return self::selectDB('catalog_order_status', null, 'id');
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
		$names = array('SPEI','Cheque','Pago en taquilla');

		$payMethod->id = $paymentMethodId;
		$payMethod->name = $names[$paymentMethodId];

		return $payMethod;
	}

	public static function getProyectFromId($orden){
		$proyKeyId = array();

		$proyectos = self::getProyects($orden->integradoId);

		// datos del proyecto y subproyecto involucrrado
		foreach ( $proyectos as $key => $proy) {
			$proyKeyId[$proy->id_proyecto] = $proy;
		}

		if(array_key_exists($orden->id, $proyKeyId)) {
			$orden->proyecto = $proyKeyId[$orden->id];

			if($orden->proyecto->parentId > 0) {
				$orden->sub_proyecto	= $orden->proyecto;
				$orden->proyecto		= $proyKeyId[$orden->proyecto->parentId];
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

	private static function getTxDataByTxId($txId) {

		$txstp = new stdClass;
	    $txstp->referencia = 'A458455A554SJHS445AA2D';
	    $txstp->integradoId = 1;
	    $txstp->date = 1419897600000;
	    $txstp->amount = '34014.100';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A5S1S5200S4AA2D';
	    $txstp->integradoId = 1;
	    $txstp->date = 1408632474029;
	    $txstp->amount = '1520.2145';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A55422S5S555220';
	    $txstp->integradoId = 1;
	    $txstp->date = 1419897603300;
	    $txstp->amount = '34240.10';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A55422255F6AA2D';
	    $txstp->integradoId = 1;
	    $txstp->date = 1419897602100;
	    $txstp->amount = '8340.10';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A55421S555S17S74';
	    $txstp->integradoId = 1;
	    $txstp->date = 1419897600023;
	    $txstp->amount = '1340.10';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A554222115s11s5s';
	    $txstp->integradoId = 1;
	    $txstp->date = 1408632474029;
	    $txstp->amount = '34540.10';


	    $array[] = $txstp;

	    $txstp = new stdClass;
	    $txstp->referencia = 'A458455A55422255F6AA2D';
	    $txstp->integradoId = 1;
	    $txstp->date = 1419897600000;
	    $txstp->amount = '340.10';

	    $array[] = $txstp;

		$txId = (int)$txId;

	    return $array[$txId];
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

    public function insertDB($tabla, $columnas=null, $valores=null, $last_inserted_id = null){
        $columnas = is_null($columnas)?$this->columnas:$columnas;
        $valores = is_null($valores)?$this->valores:$valores;

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
        $verbose = fopen('curl.log', 'w');
        $ch = curl_init();

        switch($this->getHttpType()) {
            case ('POST'):
                $options = array(
                    CURLOPT_POST 			=> true,
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
            echo "Verbose information:\n<pre>", htmlspecialchars( $verboseLog ), "</pre>\n" . curl_errno( $ch ) . curl_error( $ch );
        }

        $this->result->code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
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
	    $integrado->cantidadAuthNecesarias = 1;

	    $tableAuth = $orderType.'_auth';
	    $order->auths = getFromTimOne::getOrdenAuths($order->id, $tableAuth);

	    $order->hasAllAuths = $integrado->cantidadAuthNecesarias == count($order->auths);
	    $order->canChangeStatus = $this->validStatusChange($order, $orderNewStatus);

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
			case 0:
				$return = $orderNewStatus == 1 && $order->hasAllAuths;
				break;
			case 1:
				$return = $orderNewStatus == 2 && $order->hasAllAuths;
				break;
			case 2:
				$return = $orderNewStatus == 3 && $order->hasAllAuths;
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
        //metodo en el que se va a enviar los datos a TIMONE para que registre la transacción y no debería regresar el id de esta.
    }

    public function generarFactturaComisiones($dataFactura){
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
class ReportBalance extends getFromTimOne {
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
	protected $request;

	/**
	 * @param $params array(integradoId => $integradoId, balanceId  => $balanceId = null)
	 */
	function __construct( $params ) {
		list( $fechaInicio, $fechaFin ) = $this->setNewDatesIniciofin();

		$this->request->integradoId = $params['integradoId'];

		if ( isset( $params['balanceId'] ) ) {
			if ( $params['balanceId'] != 0 ) {
				$this->request->balanceId   = $params['balanceId'];
				$this->request->startDate   = $fechaInicio;
				$this->request->endDate     = $fechaFin;
			}
		}

	}

	public function generateBalance( ) {
		$respuesta = null;

		// TODO sustituir mock
		for ( $i = 1; $i <= 10; $i ++ ) {
			$b = new ReportBalance( array('integradoId' => $this->request->integradoId, 'balanceId' => $this->request->balanceId) );
			$b->id                                  = $i;
			$b->integradoId                         = $i;

			$array[] = $b;
		}

		foreach ( $array as $key => $value ) {
			if ( $this->request->integradoId == $value->integradoId && $this->request->balanceId == null ) {
				$this->mockData( $value );
				getFromTimOne::convierteFechas( $value );
				$respuesta[] = $value;
			} elseif ( $this->request->integradoId == $value->integradoId && $this->request->balanceId == $value->id ) {
				$this->mockData( $value );
				getFromTimOne::convierteFechas( $value );
				$this->setDatesForDisplay($value);
				$respuesta[] = $value;
			}
		}

		return $respuesta;
	}

	/**
	 * @param $b
	 */
	public function mockData( $b ) {
		$b->numBalance                      = 1;
		$b->proyectId                       = 1;
		$b->createdDate                     = 1388880000000;
		$b->currency                        = 'MXN';
		$b->paymentType                     = 0;
		$b->status                          = 0;
		$b->observaciones                   = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
		$b->pasivo->cuentasPorPagar         = $this->getCxP()->neto;; // suma historica de CxP
		$b->pasivo->ivaVentas               = $this->getIvaVentasPeriodo();
		$b->pasivo->total                   = $b->pasivo->cuentasPorPagar + $b->pasivo->ivaVentas;
		$b->activo->bancoSaldoEndDate       = $this->getBancoSaldoEndDate();
		$b->activo->cuentasPorCobrar        = $this->getCxC()->neto;
		$b->activo->ivaCompras              = $this->getIvaComprasPeriodo();
		$b->activo->total                   = $b->activo->cuentasPorCobrar + $b->activo->ivaCompras + $b->activo->bancoSaldoEndDate;
		$b->capital->ejecicioAnterior       = 0;
		$b->capital->totalEdoResultados     = 750;
		$b->depositos->ejecicioAnterior     = 0;
		$b->depositos->actual               = 600;
		$b->retiros->ejecicioAnterior       = 0;
		$b->retiros->actual                 = 350;

		$b->capital->total                  = ($b->capital->ejecicioAnterior + $b->capital->totalEdoResultados + $b->depositos->ejecicioAnterior + $b->depositos->actual) - ($b->retiros->ejecicioAnterior + $b->retiros->actual);
	}

	private function getIvaVentasPeriodo( ) {
		$ivas = array();
		$invoices   = getFromTimOne::getFacturasVenta($this->request->integradoId);

		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
		foreach ( $invoices as $fact ) {
			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);

			$testDates = ($fact->timestamps->createdDate >= $this->request->startDate->timestamp && $fact->timestamps->createdDate <= $this->request->endDate->timestamp);
			if ( $testStatus && $testDates) {
				$ivas[] = $fact->iva;
			}
		}

		return array_sum($ivas);
	}

	private function getIvaComprasPeriodo() {
		$ivas = array();
		$invoices   = getFromTimOne::getOrdenesVenta($this->request->integradoId);

		$unpaidStatusCatalog = parent::getUnpaidOrderStatusCatalog();
		foreach ( $invoices as $fact ) {
			$testStatus = in_array( $fact->status->id, $unpaidStatusCatalog);

			$testDates = ($fact->timestamps->createdDate >= $this->request->startDate->timestamp && $fact->timestamps->createdDate <= $this->request->endDate->timestamp);
			if ( $testStatus && $testDates) {
				$ivas[] = $fact->iva;
			}
		}

		return array_sum($ivas);
	}

	private function getCxP() {
		$orders = getFromTimOne::getOrdersCxP($this->request->integradoId);

		$respuesta = $this->sumOrders($orders->odc);

		return $respuesta;
	}

	private function getCxC() {
		$orders = getFromTimOne::getOrdersCxC($this->request->integradoId);

		$respuesta = $this->sumOrders($orders->odv);

		return $respuesta;
	}

	/**
	 * @return array
	 */
	protected function setNewDatesIniciofin() {
		$timeZone    = new DateTimeZone( 'America/Mexico_City' );
		$fechaInicio = new DateTime( 'first day of January', $timeZone );
		$fechaFin    = new DateTime( 'first day of this month', $timeZone );
		$fechaFin->setTime( 0, 0, 0 );
		$fechaInicio->timestamp = $fechaInicio->getTimestamp();
		$fechaFin->timestamp    = $fechaFin->getTimestamp();

		return array ( $fechaInicio, $fechaFin );
	}

	public static function getFlujo( $integradoId ) {
		$respuesta = null;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1388880000000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1393718400000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1396137600000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1398816000000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1401408000000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;


		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1404086400000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1409356800000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1412035200000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1414627200000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1417305600000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		$flujo                = new stdClass;
		$flujo->id            = 1;
		$flujo->integradoId   = 1;
		$flujo->numflujo      = 1;
		$flujo->proyectId     = 1;
		$flujo->created       = 1419897600000;
		$flujo->currency      = 'MXN';
		$flujo->paymentType   = 0;
		$flujo->status        = 0;
		$flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

		$array[] = $flujo;

		foreach ( $array as $key => $value ) {
			if ( $integradoId == $value->integradoId ) {
				getFromTimOne::convierteFechas( $value );
				$respuesta[] = $value;
			}
		}

		return $respuesta;
	}

	public function getBalances() {
		for ( $i = 1; $i <= 10; $i ++ ) {
			$b = new ReportBalance( array('integradoId' => $i, 'balanceId' => $i) );
			$b->id                              = $i;
			$b->integradoId                     = $i;
			$b->numBalance                      = 1;
			$b->proyectId                       = 1;
			$b->createdDate                     = 1388880000000;
			$b->currency                        = 'MXN';
			$b->paymentType                     = 0;
			$b->status                          = 0;
			$b->observaciones                   = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
			$b->pasivo->cuentasPorPagar         = 800; // suma historica de CxP
			$b->pasivo->ivaVentas               = 300;
			$b->pasivo->total                   = $b->pasivo->cuentasPorPagar + $b->pasivo->ivaVentas;
			$b->capital->ejecicioAnterior       = 100;
			$b->capital->totalEdoResultados     = 500;
			$b->capital->total                  = 600;
			$b->depositos->ejecicioAnterior  = 600;
			$b->depositos->total                = 600*$this->integradoId;
			$b->retiros->ejecicioAnterior    = 600;
			$b->retiros->total                  = 600;

			if ( $this->request->integradoId == $b->integradoId ) {
				$array[] = $b;
			}
		}

		return $array;
	}

	private function setDatesForDisplay( $value ) {
		$value->period->startDate   = date('d-m-Y',$value->request->startDate->timestamp);
		$value->period->endDate     = date('d-m-Y',$value->request->endDate->timestamp);
	}

	private function getBancoSaldoEndDate() {
		// TODO: Operar el saldo con las Tx para sacar el saldo a cirre de periodo del balance
		return (float)946;
	}

	private function sumOrders( $orders ) {
		$neto = 0;
		$iva = 0;
		$total = 0;
		foreach ( $orders as $order ) {
			$neto = $neto + $order->subTotalAmount;
			$iva = $iva + $order->iva;
			$total = $total + $order->totalAmount;
		}
		$obj = new stdClass();
		$obj->neto = $neto;
		$obj->iva = $iva;
		$obj->total = $total;

		return $obj;
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