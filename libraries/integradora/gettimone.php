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
        return $datos;
    }

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

    public static function getTxConciliaciones($where){

        $txs = self::selectDB('conciliacion_banco_integrado',$where,'','getFromTimOne');

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
                    $value->rfc = $general->rfc;
                    $value->tradeName = $general->tradeName;
                    $value->corporateName = $general->corporateName;
                    $value->contact = $general->contact;
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

        if($type == 0){
            foreach ($response as $value) {
                if($value->type == $type){
                    $clientes[] = $value;
                }
            }
            $response = $clientes;
        }elseif($type == 1){
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

    public static function getOrdenesCompra($integradoId = null, $idOrden = null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_compra');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->proyecto        = (INT)$value->proyecto;
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
        }

        return $orden;
    }

    public static function getOrdenesDeposito($integradoId = null, $idOrden = null){
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_deposito');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->integradoId     = (INT)$value->integradoId;
            $value->numOrden        = (INT)$value->numOrden;
            $value->status          = (INT)$value->status;
            $value->paymentMethod   = (INT)$value->paymentMethod;
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->attachment      = (STRING)$value->attachment;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
        }

        return $orden;
    }

    public static function getOrdenesVenta($integradoId = null, $idOrden = null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_venta');

        //Cambio el tipo de dato para las validaciones con (===)
        foreach ($orden as $key => $value) {
            $value->id             = (INT)$value->id;
            $value->integradoId    = (INT)$value->integradoId;
            $value->numOrden       = (INT)$value->numOrden;
            $value->projectId      = (INT)$value->projectId;
            $value->projectId2     = (INT)$value->projectId2;
            $value->clientId       = (INT)$value->clientId;
            $value->account        = (INT)$value->account;
            $value->paymentMethod  = (INT)$value->paymentMethod;
            $value->conditions     = (INT)$value->conditions;
            $value->placeIssue     = (INT)$value->placeIssue;
            $value->status         = (INT)$value->status;
            $value->productos      = (STRING)$value->productos;
            $value->createdDate    = (STRING)$value->createdDate;
            $value->paymentDate    = (STRING)$value->paymentDate;

        }

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

    public static function getOrdenesRetiro($integradoId = null, $idOrden= null) {
        $orden = self::getOrdenes($integradoId, $idOrden, 'ordenes_retiro');

        foreach ($orden as $value) {
            $value->id              = (INT)$value->id;
            $value->integradoId     = (INT)$value->integradoId;
            $value->numOrden        = (INT)$value->numOrden;
            $value->paymentMethod   = (INT)$value->paymentMethod;
            $value->status          = (INT)$value->status;
            $value->totalAmount     = (FLOAT)$value->totalAmount;
            $value->createdDate     = (STRING)$value->createdDate;
            $value->paymentDate     = (STRING)$value->paymentDate;
        }

        return $orden;
    }

    public static function getBalances($integradoId)
    {
        $respuesta = null;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1388880000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1393718400000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1396137600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1398816000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1401408000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;


        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1404086400000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1409356800000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1412035200000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1414627200000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1417305600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1419897600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
    }

    public static function getFlujo($integradoId)
    {
        $respuesta = null;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1388880000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1393718400000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1396137600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1398816000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1401408000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;


        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1404086400000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1409356800000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1412035200000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1414627200000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1417305600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1419897600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
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

    public static function getTxSinMandato($userId = null) {
	    $txs = getFromTimOne::selectDB( 'txs_timone_mandato', 'idOrden IS NULL' );

	    foreach ( $txs as $transaction ) {
		    $transaction->data = getFromTimOne::getTxDataByTxId($transaction->id);
	    }
	    var_dump( $txs );
	    exit;

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

    public static function getTxSTPbyRef( $ref ) {
        $txs = self::getTxSinMandato();

        foreach ( $txs as $tx ) {
            if ($tx->referencia == $ref) {
                return $tx;
            }
        }
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

		$montoComision = isset($comision) ? $orden->comprobante['TOTAL'] * ($comision->rate / 100) : null;

		return $montoComision;
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


