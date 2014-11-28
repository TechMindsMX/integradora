<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.rutas');

class getFromTimOne{
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

    public static function selectDB($table, $where){
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
            if ($db->getNumRows($db->query()) == 1) {
                $results = $db->loadObject();
            } else {
                $results = $db->loadObjectList();
            }
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

	public static function getOrdenes($integradoId = null, $idOrden = null, $table){
		$where = null;
		if(isset($idOrden)){
			$where = 'id = '.$idOrden;
		}elseif(isset($integradoId)){
			$where = 'integradoId = '.$integradoId;
		}

        $ordenes = self::selectDB($table, $where);

        if(count($ordenes) == 1){
            self::convierteFechas($ordenes);
        }else {
            foreach ($ordenes as $orden) {
                self::convierteFechas($orden);
            }
        }

        return $ordenes;
    }

	public static function getOrdenesCompra($integradoId = null, $idOrden = null) {
		return self::getOrdenes($integradoId, $idOrden, 'ordenes_compra');
	}

	public static function getOrdenesDeposito($integradoId = null, $idOrden = null){
		return self::getOrdenes($integradoId, $idOrden, 'ordenes_deposito');
    }

	public static function getOrdenesVenta($integradoId = null, $idOrden = null) {
		return self::getOrdenes($integradoId, $idOrden, 'ordenes_venta');
    }

	public static function getOrdenesRetiro($integradoId = null, $idOrden= null) {
		return self::getOrdenes($integradoId, $idOrden, 'ordenes_retiro');
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

    public static function getFactura() {
        $factura = null;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 1;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 1;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-06-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 2;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "120 - ";
        $factura->Comprobante->folio                                    = "1120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-05-13T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 3;
        $factura ->integradoId                                          = 3;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "150 - ";
        $factura->Comprobante->folio                                    = "450";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2013-05-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 4;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "1010 - ";
        $factura->Comprobante->folio                                    = "11220";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2011-07-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 5;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 1;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-05-24T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 6;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "4110 - ";
        $factura->Comprobante->folio                                    = "1790";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-09-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->receptor->domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 7;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-12-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        return $array;


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

    public static function getTxSTP($userId = null)
    {
        $txstp = new stdClass;
        $txstp->referencia = 'A458455A554SJHS445AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897600000;
        $txstp->amount = '34014.100';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A5S1S5200S4AA2D';
        $txstp->userId = 1;
        $txstp->date = 1408632474029;
        $txstp->amount = '1520.2145';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422S5S555220';
        $txstp->userId = 1;
        $txstp->date = 1419897603300;
        $txstp->amount = '34240.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422255F6AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897602100;
        $txstp->amount = '8340.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55421S555S17S74';
        $txstp->userId = 1;
        $txstp->date = 1419897600023;
        $txstp->amount = '1340.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A554222115s11s5s';
        $txstp->userId = 1;
        $txstp->date = 1408632474029;
        $txstp->amount = '34540.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422255F6AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897600000;
        $txstp->amount = '340.10';

        $array[] = $txstp;

        return $array;
    }

    public static function getMedidas(){
        $respuesta['litros'] 			= 'litros';
        $respuesta['Metros'] 			= 'Metros';
        $respuesta['Metros Cúbicos'] 	= 'Metros Cúbicos';

        return $respuesta;
    }

    public static function convierteFechas($objeto){
        foreach ($objeto as $key => $value) {
            if($key == 'createdDate' || $key == 'paymentDate' || $key == 'created' || $key == 'payment'){
                $propiedad = $key.'numero';
                $objeto->$propiedad = $value;
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
        $txs = self::getTxSTP();

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

	public static function getTriggersComisiones() {
		$triggers = array('oddpagada' => 'Orden de Depósito pagada', 'odcpagada' => 'Orden de Compra pagada', 'fecha' => 'Según recurrencia');

		return $triggers;
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

    public function getNextOrderNumber($tipo, $integrado){
        $db		= JFactory::getDbo();

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
        }

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
        foreach ($arreglo as $key => $value) {
            $this->columnas[] = $key;
            $this->valores[] = $db->quote($value);
            $this->set[] = $db->quoteName($key).' = '.$db->quote($value);
        }
    }

    public function saveProyect($data){
        $db		= JFactory::getDbo();
        foreach ($data as $key => $value) {
            $columnas[] = $key;
            $valores[] = $db->quote($value);
        }

        $this->insertDB('integrado_proyectos', $columnas, $valores);
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

    public function insertDB($tabla, $columnas=null, $valores=null){
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
            echo $e->getMessage();
            $return = false;
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
            echo $e->getMessage();
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

            $return = '';

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

    /**
     * @return mixed
     */
    public function getHttpType () {
        return strtoupper($this->httpType);
    }


}

class comisionItem{
    public $id;
    public $description;
    public $type;
    public $frequencyTime;
    public $status;
    public $typeName;
    public $statusName;
    public $frequencyMsg;
    public $amount;
    public $rate;
}