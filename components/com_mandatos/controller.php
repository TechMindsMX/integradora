<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');
jimport('integradora.notifications');
jimport('integradora.facturasComision');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosController extends JControllerLegacy {
    public $factutaComisiones;

    public function __construct(){
        parent::__construct();
        $this->app			= JFactory::getApplication();
        $this->document     = JFactory::getDocument();
        $this->currUser	 	= JFactory::getUser();
        $this->input_data	= $this->app->input;
        $this->post		 	= $this->input_data->getArray();

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        if($this->currUser->guest){
            $this->app->redirect('index.php?option=com_users&view=login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
        }
        if(is_null($this->integradoId)){
            $this->app->redirect('index.php?option=com_integrado&view=integrado&Itemid=207', JText::_('MSG_REDIRECT_INTEGRADO'), 'Warning');
        }
        else {
            $integrado	    = new IntegradoSimple($this->integradoId);
            $canOperate     = $integrado->canOperate();
            if(!$canOperate){
                $this->app->redirect('index.php?option=com_integrado&view=integrado&Itemid=207', JText::_('MSG_REDIRECT_INTEGRADO_CANT_OPERATE'), 'Warning');
            }
        }
    }

    function editarproyecto(){
        $post           = array('integradoId'=>'INT', 'id_proyecto'=>'INT');
        $data 			= $this->input_data->getArray($post);
        $proyectos 		= getFromTimOne::getProyects(null, $data['id_proyecto']);
        $count          = 0;

        if($this->currUser->guest){
            $this->app->redirect('index.php?option=com_users&view=login');
        }

        foreach ($proyectos as $key => $value) {
            if($key == $data['id_proyecto']){
                if($value->parentId == 0){
                    $this->app->redirect('index.php?option=com_mandatos&view=proyectosform&id_proyecto='.$data['id_proyecto']);
                }else{
                    $this->app->redirect('index.php?option=com_mandatos&view=subproyectosform&id_proyecto='.$data['id_proyecto']);
                }
            }else{
                $count++;
            }
        }

        if( $count == count($proyectos) ){
            $this->app->redirect('index.php?option=com_mandatos&view=proyectoslist');
        }
        exit;
    }

    function editarproducto(){
        $post           = array('id_producto'=>'INT');
        $data 			= $this->input_data->getArray($post);
        $productos 		= getFromTimOne::getProducts($this->integradoId);
        $count          = 0;

        if($this->currUser->guest){
            $this->app->redirect('index.php?option=com_users&view=login');
        }

        foreach ($productos as $key => $value) {
            if($data['id_producto'] == $value->id_producto){
                $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=productosform&id_producto='.$data['id_producto']));
            }else{
                $count++;
            }
        }

        if( $count == count($productos) ){
            $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=productoslist'));
        }
        exit;
    }

    function editarclientes(){
        $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=clientes'), 'Por el momento no es posible crear ni editar');
    }

    function agregarBancoCliente(){

	    $validator   = new validador();
	    $diccionario = array (
		    'db_banco_codigo'   => array ( 'alphaNumber' => true, 'length' => 3, 'required' => true ),
		    'db_banco_cuenta'   => array ( 'required' => true ),
		    'db_banco_sucursal' => array ( 'required' => true ),
		    'db_banco_clabe'    => array ( 'banco_clabe' => $this->post['db_banco_codigo'], 'length' => 18, 'required' => true )
	    );
	    $validacion  = $validator->procesamiento( $this->post, $diccionario );

	    if ( $validator->allPassed() ) {

		    $existe = getFromTimOne::searchBancoByClabe($this->post['db_banco_clabe']);

		    // guarda la cuenta si no existe
		    if (is_null($existe)) {
			    list( $respuesta, $existe, $newId, $db, $data, $save ) = Integrado::saveBankIfNew($this->post['integradoId']);
			    $existe = (OBJECT)$respuesta;
			    $existe->datosBan_id = $newId;
		    } else {
			    $respuesta['success'] = true;
			    $respuesta = array_merge( $respuesta, (array)$existe );
		    }

		    if($respuesta['success'] == true) {

			    $idClipro = isset($newId) ? $newId : $existe->datosBan_id;
			    $respuesta['datosBan_id'] = $idClipro;

			    // se busca la relacion del cliente y el integrado
			    $tableRelacion 		= 'integrado_clientes_proveedor';
			    $db = JFactory::getDbo();
			    $whereRelacion      = $db->quoteName('integradoId').' = '. $db->quote($this->integradoId) .' && '.$db->quoteName('integradoIdCliente').' = '. $db->quote($this->post['integradoId']);
			    $relacion           = getFromTimOne::selectDB($tableRelacion,$whereRelacion);

			    if ( ! empty( $relacion[0] ) ) {
				    $relacion           = $relacion[0];
			    }

			    // Si no existe la relacion la creamos
			    $bancos = isset($relacion->bancos) ? json_decode($relacion->bancos, true) : array();

			    if( !in_array( $existe->datosBan_id, $bancos) && $this->integradoId != $this->post['integradoId'] ) {
				    array_push($bancos, $existe->datosBan_id);

				    $datos   = array('bancos' => json_encode($bancos));

				    $save = new sendToTimOne();
				    $save->formatData($datos);
				    $where = $db->quoteName('integradoId').' = '. $db->quote($this->integradoId) .' && '.$db->quoteName('integradoIdCliente').' = '. $db->quote($this->post['integradoId']);
				    $update = $save->updateDB('integrado_clientes_proveedor', null, $where );

				    $idClipro = isset($newId) ? $newId : $existe->datosBan_id;
				    $respuesta['success'] = $update;
				    $respuesta['banco_codigo'] = $this->post['db_banco_codigo'];
				    $respuesta['banco_cuenta'] = $this->post['db_banco_cuenta'];
				    $respuesta['banco_sucursal'] = $this->post['db_banco_sucursal'];
				    $respuesta['banco_clabe'] = $this->post['db_banco_clabe'];
				    $respuesta['datosBan_id'] = $idClipro;

			    }
		    }
	    } else {
		    $respuesta['success'] = false;
		    $respuesta['msg'] = $validacion;
	    }

	    $this->document->setMimeEncoding('application/json');
	    echo json_encode($respuesta);
    }

    function deleteBanco(){
        $db	        = JFactory::getDbo();
        $save       = new sendToTimOne();
        $post       = array('datosBan_id' => 'INT');
        $data 		= $this->input_data->getArray($post);
        $table 		= 'integrado_datos_bancarios';
        $where      = $db->quoteName('integradoId').' = '.$this->integradoId.' && '.$db->quoteName('datosBan_id').' = '. (INT)$data['datosBan_id'];

        $respuesta['msg'] = $save->deleteDB($table,$where);

        if($respuesta['msg']) {
            $respuesta['success'] = true;
        }else{
            $respuesta['success'] = false;
        }

        $this->document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    function saveProducts(){
        $campos      = array(
            'id_producto'=>'INT',
            'productName'=>'STRING',
            'measure'=>'STRING',
            'price'=>'STRING',
            'iva'=>'STRING',
            'ieps'=>'STRING',
            'currency'=>'STRING',
            'status'=>'STRING',
            'description'=>'STRING');
        $data        = $this->input_data->getArray($campos);
        $data['integradoId'] = $this->integradoId;

        $id_producto = $data['id_producto'];
        $save        = new sendToTimOne();

        unset($data['id_producto']);

        if($id_producto == 0){
            $save->saveProduct($data);
        }else{
            $save->updateProduct($data, $id_producto);
        }



        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=productoslist');
    }

    function  cargaProducto(){
        $this->document->setMimeEncoding('application/json');
        $data = $this->input_data->getArray();
        $productos = getFromTimOne::getProducts($this->integradoId);
        foreach ($productos as $key => $val) {
            if($data['id'] == $val->id){
                $producto = $val;
            }
        }

        echo json_encode($producto);
    }

    public function saveCliPro(){
        $db	        = JFactory::getDbo();
        $save       = new sendToTimOne();
        $datosQuery = array();
        $arrayPost  = array(
            'idCliPro'                      => 'INT',
            'co_email1'                     => 'STRING',
            'co_email2'                     => 'STRING',
            'co_email3'                     => 'STRING',
            'co_tel_fijo1'                  => 'STRING',
            'co_tel_fijo2'                  => 'STRING',
            'co_tel_fijo3'                  => 'STRING',
            'co_tel_fijo_extension1'        => 'STRING',
            'co_tel_fijo_extension2'        => 'STRING',
            'co_tel_fijo_extension3'        => 'STRING',
            'co_tel_movil1'                 => 'STRING',
            'co_tel_movil2'                 => 'STRING',
            'co_tel_movil3'                 => 'STRING',
            'db_banco_clabe'                => 'STRING',
            'db_banco_cuenta'               => 'STRING',
            'db_banco_nombre'               => 'STRING',
            'db_banco_sucursal'             => 'STRING',
            'de_calle'                      => 'STRING',
            'de_cod_postal'                 => 'STRING',
            'de_num_exterior'               => 'STRING',
            'de_num_interior'               => 'STRING',
            'de_razon_social'               => 'STRING',
            'de_rfc'                        => 'STRING',
            'dp_calle'                      => 'STRING',
            'dp_cod_postal'                 => 'STRING',
            'dp_curp'                       => 'STRING',
            'dp_fecha_nacimiento'           => 'STRING',
            'dp_nacionalidad'               => 'STRING',
            'dp_nom_comercial'              => 'STRING',
            'dp_nombre_representante'       => 'STRING',
            'dp_num_exterior'               => 'STRING',
            'dp_num_interior'               => 'STRING',
            'dp_rfc'                        => 'STRING',
            'dp_sexo'                       => 'STRING',
            'pj_pers_juridica'              => 'STRING',
            't1_instrum_fecha'              => 'STRING',
            't1_instrum_estado'             => 'STRING',
            't1_instrum_nom_notario'        => 'STRING',
            't1_instrum_notaria'            => 'STRING',
            't1_instrum_num_instrumento'    => 'STRING',
            't2_instrum_fecha'              => 'STRING',
            't2_instrum_estado'             => 'STRING',
            't2_instrum_nom_notario'        => 'STRING',
            't2_instrum_notaria'            => 'STRING',
            't2_instrum_num_instrumento'    => 'STRING',
            'pn_instrum_fecha'              => 'STRING',
            'pn_instrum_estado'             => 'STRING',
            'pn_instrum_nom_notario'        => 'STRING',
            'pn_instrum_notaria'            => 'STRING',
            'pn_instrum_num_instrumento'    => 'STRING',
            'rp_instrum_fecha'              => 'STRING',
            'rp_instrum_estado'             => 'STRING',
            'rp_instrum_num_instrumento'    => 'STRING',
            'tab'                           => 'STRING',
            'tp_tipo_alta'                  => 'STRING',
            'tp_status'                     => 'STRING',
            'tp_monto'                      => 'FLOAT');

        $tab        = 'tipo_alta';
        $data       = $this->input_data->getArray($arrayPost);
        $data['integradoId'] = $this->integradoId;
        $idCliPro   = $data['idCliPro'];
        $datosQuery['setUpdate'] = array();

        $this->dataCliente  = (object) $data;

        $validations = $this->validatePost($data);
        if($validations->allPassed()) {

            // verificación que no sea el mismo integrado
//            $currentIntegrado = new IntegradoSimple($this->integradoId);

            if($idCliPro == 0){
                $idCliPro = getFromTimOne::saveNewIntegradoIdAndReturnIt($data['pj_pers_juridica']);
                $data['idCliPro'] = $idCliPro;
            }

            switch($data['tab']){
                case 'tipoAlta_btn':
                    $table 		= 'integrado_clientes_proveedor';
                    $where      = $db->quoteName('integradoIdCliente').' = '. $db->quote($idCliPro) .' && integradoId = '. $db->quote($this->integradoId);
                    $existe     = getFromTimOne::selectDB($table,$where);

                    $columnas[] = 'integradoId';
                    $valores[]	= $this->integradoId;
                    $columnas[] = 'integradoIdCliente';
                    $valores[]	= $idCliPro;

                    $datosQuery['columnas']  = $columnas;
                    $datosQuery['valores']   = $valores;

                    $datosQuery = getFromTimOne::limpiarPostPrefix( $data, 'tp_', $datosQuery );
                    $tab = 'juridica';
                    break;
                case 'juridica':
                    $table 		= 'integrado';
                    $where      = $db->quoteName('integradoId').' = '. $db->quote($idCliPro);
                    $existe     = getFromTimOne::selectDB($table, $where);

                    $datosQuery['setUpdate'] = array($db->quoteName('pers_juridica').' = '.$db->quote($data['pj_pers_juridica']));
                    $tab = 'basic-details';
                    break;
                case 'personales':
                    $table 		= 'integrado_datos_personales';
                    $where      = $db->quoteName('integradoId').' = '. $db->quote($idCliPro);
                    $existe     = getFromTimOne::selectDB($table, $where);

                    $columnas[] = 'integradoId';
                    $valores[]	= $idCliPro;

                    $datosQuery['columnas'] = $columnas;
                    $datosQuery['valores']  = $valores;

                    $datosQuery = getFromTimOne::limpiarPostPrefix( $data, 'dp_', $datosQuery );
                    if(empty($existe)){
                        self::safeContacto($data, $idCliPro);
                    }
                    if($data['pj_pers_juridica'] == 2 ){
                        $tab = 'files';
                    }elseif($data['pj_pers_juridica'] == 1){
                        $tab = 'empresa';
                    }
                    break;
                case 'empresa':
                    $table = 'integrado_datos_empresa';
                    $where = $db->quoteName('integradoId').' = '. $db->quote($idCliPro);
                    $existe = getFromTimOne::selectDB($table, $where);
                    $columnas[] = 'integradoId';
                    $valores[]	= $idCliPro;

                    //self::saveInstrumentos($data);

                    $datosQuery['columnas'] = $columnas;
                    $datosQuery['valores']  = $valores;

                    $datosQuery = getFromTimOne::limpiarPostPrefix( $data, 'de_', $datosQuery );
                    if($data['tp_tipo_alta'] == 1 || $data['tp_tipo_alta'] == 2) {
                        $tab = 'banco';
                    }else {
                        $tab = 'files';
                    }
                    break;
            }

            if(empty($existe)) {
                $save->insertDB($table, $datosQuery['columnas'], $datosQuery['valores']);

                if( $table == 'integrado_datos_personales'){

                    $this->sendEmail(__METHOD__);

                }

            }else{
                $save->updateDB($table,$datosQuery['setUpdate'],$where);
            }

            if($data['pj_pers_juridica'] == 2){
                $tab = 'banco';
            }

            $response['idCliPro'] = $idCliPro;
            $response['success'] = true;
            $response['nextTab'] = $tab;

        } else {
            $response = $validations->getRespuestas();
        }

        $this->document->setMimeEncoding('application/json');

        echo json_encode($response);
    }

    //carga los archivos y guarda en la base las url donde estan guardadas, al final hace una redirección.
    function uploadFiles(){

        $idCliPro	= JFactory::getApplication()->input->get('idCliPro', null, 'INT');

	    $dbq = JFactory::getDbo();
	    $result     = getFromTimOne::selectDB('integrado_clientes_proveedor', 'integradoIdCliente = '. $dbq->quote($idCliPro) );
        $integrado_id = $result[0]->integradoIdCliente;

        $resultado = sendToTimOne::uploadFiles($integrado_id);

        $app = JFactory::getApplication();
        if ($resultado) {
            $app->enqueueMessage('LBL_DATOS_GUARDADOS');
        }
        $url = 'index.php?option=com_mandatos&view=clienteslist';

        $app->redirect($url, false);

    }

    public static function safeContacto($data, $integradoId){
        $db         = JFactory::getDbo();
        $save       = new sendToTimOne();
        $datosQuery = array('setUpdate'=>array());
        $table 		= 'integrado_contacto';
        $where      = $db->quoteName('integradoId').' = '. $db->quote($integradoId);
        $existe     = getFromTimOne::selectDB($table, $where);

        $columnas[] = 'integradoId';
        $valores[]	= $db->quote($integradoId);

        $datosQuery['columnas'] = $columnas;
        $datosQuery['valores']  = $valores;

        $datosQuery = getFromTimOne::limpiarPostPrefix( $data, 'co_', $datosQuery );

        $columnas = $datosQuery['columnas'];
        $valores = $datosQuery['valores'];

        foreach ($columnas as $key => $value) {
            $arreglo[$value] = $valores[$key];
        }

        for($i = 0; $i < 3; $i++){
            $columnas = array();
            $valores = array();

            $columnas[] = 'integradoId';
            $columnas[] = 'telefono';
            $columnas[] = 'movil';
            $columnas[] = 'ext';
            $columnas[] = 'correo';

            $tel = 'tel_fijo'.($i+1);
            $movil = 'tel_movil'.($i+1);
            $ext = 'tel_fijo_extension'.($i+1);
            $correo = 'email'.($i+1);

            $valores[] = $db->quote($integradoId);
            $valores[] = $arreglo[$tel];
            $valores[] = $arreglo[$movil];
            $valores[] = $arreglo[$ext];
            $valores[] = $arreglo[$correo];

            $save->insertDB('integrado_contacto',$columnas,$valores);
        }
    }

    function searchProducts(){
        $this->document->setMimeEncoding('application/json');

        $respuesta = array();
        $db     = JFactory::getDbo();
        $campos = array('productName'=>'STRING');
        $data   = $this->input_data->getArray($campos);

        $where  = $db->quoteName('productName').' = '.$db->quote($data['productName']).' AND '.$db->quoteName('integradoId').' = '. $db->quote($this->integradoId) .' AND '.$db->quoteName('status').' = 1';

        $producto = getFromTimOne::selectDB('integrado_products',$where);

        if(!empty($producto)) {
            $respuesta['success'] = true;
            $respuesta['datos'] = $producto[0];
        }else{
            $respuesta['success'] = false;
            $respuesta['msg'] = 'No existe el producto';
        }

        echo json_encode($respuesta);
    }

    public function cancelOdv(){
        $data  = $this->input_data->getArray();
        $idOdv = $data['idOdv'];
        $delete = new sendToTimOne();

        $delete->deleteDB('ordenes_venta','id = '.$idOdv);
        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odvlist');
    }

    public function disable(){
        $post = array('type' => 'STRING',
            'id' => 'STRING',
            'accion' => 'STRING');
        $data = $this->input_data;
        $data = $data->getArray($post);
        $save = new sendToTimOne();

        switch($data['type']){
            case 'proyecto':
                $table = 'integrado_proyectos';
                $where = 'id_proyecto = '. (INT)$data['id'];
                break;
            case 'producto':
                $table = 'integrado_products';
                $where = 'id_producto = '. (INT)$data['id'];
                break;
            case 'cliente';
                $table = 'integrado_clientes_proveedor';
                $where = 'id = '. (INT)$data['id'];
                break;
        }

        if($data['accion']  == 'enabled'){
            $set = array('status = 0');
        }elseif($data['accion']  == 'disabled'){
            $set = array('status = 1');
        }

        $respuesta = $save->updateDB($table, $set, $where);

        $response['success'] = $respuesta;
        $response['accion'] = $data['accion'];

        $this->document->setMimeEncoding('application/json');
        echo json_encode($response);
    }

    public function generateFactComision(){

	    foreach ( $this->getAllIntegrados() as $integrado ) {

		    $factura = new facturasComision();

		    $this->factutaComisiones = $factura->generateFact($integrado->Id);

		    if(is_object($this->factutaComisiones)){
			    $this->sendEmail(__METHOD__, $integrado->Id);
			    echo 'generadas';
		    }else{
			    echo 'no se generaron';
		    }
		    exit;
	    }

    }

	public function getAllIntegrados() {
		$result = null;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select($db->quoteName('integrado_id', 'id') )
			->from($db->quoteName('#__integrado') )
        ->where($db->quoteName('status').' = 50');
		$db->setQuery($query);

		foreach ( $db->loadObjectList() as $integ ) {
			$intSim = new IntegradoSimple($integ->id);

			$result[] = $intSim->isIntegrado() ? $intSim->id : null;
		}

		return $result;
	}

    public function sendEmail($param, $id = null)
    {
        $idIntegrado = is_null($id) ? $this->integradoId : $id;

        $getCurrUser = new IntegradoSimple($idIntegrado);

	    switch ($param) {
		    case 'MandatosController::generateFactComision':
                $editTitle = array($this->factutaComisiones->id);

			    $array = array($this->factutaComisiones->getReceptor()->getDisplayName(), $this->factutaComisiones->id, date('d-m-Y'), '$'.number_format($this->factutaComisiones->totales->total, 2));
			    $notificationNumber = '19';
			    break;
		    case 'MandatosController::saveCliPro':
			    /*
			 * Notificaciones 6
			 */
			    $tipo = '';
			    if ($this->dataCliente->tp_tipo_alta == 0) {
				    $tipo = 'Cliente';
			    }
			    if ($this->dataCliente->tp_tipo_alta == 1) {
				    $tipo = 'Proveedor';
			    }
			    if ($this->dataCliente->tp_tipo_alta == 2) {
				    $tipo = 'Cliente/Proveedor';
			    }

                $editTitle = null;
			    $array = array($getCurrUser->user->name, $tipo, $this->dataCliente->dp_nom_comercial, JFactory::getUser()->name, date('d-m-Y'));
			    $notificationNumber = '6';
			    break;
	    }

	    $send = new Send_email();
        $send->setIntegradoEmailsArray($getCurrUser);

        $infoEmail = $send->sendNotifications($notificationNumber, $array, $editTitle);
        return $infoEmail;
    }

    public function validatePost($data) {
        switch ($data['tab']) {
            case 'tipoAlta_btn':
                $diccionario = array(
                    'tp_monto'           => array('float' => true,  'maxlength' => 15),
                    'tp_tipo_alta'       => array('number' => true, 'required' => true, 'maxlength' => 1)
                );
                break;
            case 'juridica':
                $diccionario = array(
                    'pj_pers_juridica'           => array('number' => true,     'required' => true, 'maxlength' => 1),
                );
                break;

            case 'personales':
                $diccionario = array(
                    'dp_nom_comercial'           => array('alphaNumber' => true,  	    'maxlength' => 150),
                    'dp_nacionalidad'            => array('alphaNumber' => true,      	'maxlength' => 45,  'required' => true),
                    'dp_sexo'                    => array('alphaNumber' => true,	   	'maxlength' => 45,  'required' => true),
                    'dp_fecha_nacimiento'        => array('date' => true,	        	'maxlength' => 10,  'required' => true),
                    'dp_rfc'                     => array('rfc_fisica' => true,      	'maxlength' => 13,  'required' => true),
                    'dp_calle'                   => array('alphaNumber' => true,	    'maxlength' => 100, 'required' => true),
                    'dp_num_exterior'            => array('alphaNumber' => true,        'maxlength' => 10,  'required' => true),
                    'dp_num_interior'            => array('alphaNumber' => true,        'maxlength' => 10),
                    'dp_cod_postal'              => array('number' => true,		        'maxlength' => 13,  'required' => true),
                    'dp_nombre_representante'    => array('string' => true,     	    'maxlength' => 150, 'required' => true),
                    'dp_curp'                    => array('alphaNumber' => true,	    'maxlength' => 18,  'required' => true),
                    'co_email1'                  => array('email' => true,		    	'maxlength' => 100, 'required' => true),
                    'co_email2'                  => array('email' => true,		    	'maxlength' => 100),
                    'co_email3'                  => array('email' => true,		    	'maxlength' => 100),
                    'co_tel_fijo1'               => array('phone' => true,            	'minlength'=>10,    'maxlength' => 10,      'required' => true),
                    'co_tel_fijo2'               => array('phone' => true,            	'minlength'=>10,    'maxlength' => 10),
                    'co_tel_fijo3'               => array('phone' => true,            	'minlength'=>10,    'maxlength' => 10),
                    'co_tel_fijo_extension1'     => array('number' => true,     	    'minlength' => 0,   'maxlength' => 5),
                    'co_tel_fijo_extension2'     => array('number' => true,     	    'minlength' => 0,   'maxlength' => 5),
                    'co_tel_fijo_extension3'     => array('number' => true,     	    'minlength' => 0,   'maxlength' => 5),
                    'co_tel_movil1'              => array('number' => true,     	    'minlength' => 13,  'maxlength' => 13),
                    'co_tel_movil2'              => array('number' => true,     	    'minlength' => 13,  'maxlength' => 13),
                    'co_tel_movil3'              => array('number' => true,     	    'minlength' => 13,  'maxlength' => 13),
                );
                break;

            case 'empresa':
                $diccionario = array(
                    'de_razon_social'            => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    'de_rfc'                     => array('rfc_moral' => true,	    	'maxlength' => 12,      'required' => true),
                    'de_calle'                   => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    'de_num_exterior'            => array('alphaNumber' => true,	    'maxlength' => 5,       'required' => true),
                    'de_cod_postal'              => array('alphaNumber' => true,	    'maxlength' => 45,      'required' => true),
                    't1_instrum_fecha'           => array('date' => true,	        	'maxlength' => 10,      'required' => true),
                    't1_instrum_notaria'         => array('alphaNumber' => true,	    'maxlength' => 45,      'required' => true),
                    't1_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 5,       'required' => true),
                    't1_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    't1_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      'required' => true),
                    'de_num_interior'            => array('alphaNumber' => true,	    'maxlength' => 5,),
                    't2_instrum_fecha'           => array('date' => true,	        	'maxlength' => 10,      ),
                    't2_instrum_notaria'         => array('alphaNumber' => true,	    'maxlength' => 13,      ),
                    't2_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    't2_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    't2_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 18,      ),
                    'pn_instrum_fecha'           => array('date' => true,	        	'maxlength' => 10,      ),
                    'pn_instrum_notaria'         => array('alphaNumber' => true, 	    'maxlength' => 18,      ),
                    'pn_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 255,     ),
                    'pn_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    'pn_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                    'rp_instrum_fecha'           => array('date' => true,	        	'maxlength' => 10,      ),
                    'rp_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                    'rp_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                );
                break;

            case 'banco':
                $diccionario = array(
                    'db_banco_clabe'             => array('banco_clabe' => $data['db_banco_codigo'],    'maxlength' => 18,  'minlength'=>18,   'required' => true),
                    'db_banco_cuenta'            => array('alphaNumber' => true,                        'maxlength' => 10,  'minlength'=>10,   'required' => true),
                    'db_banco_codigo'            => array('alphaNumber' => true,	                    'maxlength' => 3,   'required' => true),
                    'db_banco_sucursal'          => array('alphaNumber' => true,	                    'maxlength' => 10,  'required' => true),
                );
                break;
        }

        $diccionarioDefault  = array(
            'integradoId'                => array('number' => true,		    	'maxlength' => 10),
        );
        $diccionario = array_merge($diccionario, $diccionarioDefault);

        //envia la data a validacion y regresa un arreglo con los resultados para cada uno de los campos que esten llenados
        $validador = new validador();
        $resultado = $validador->procesamiento($data, $diccionario);

        return $validador;
    }

    //TODO:quitar
    public function TestFactura(){
        $save = new sendToTimOne();

        $odv = new \Integralib\OdVenta();
        $odv->setOrderFromId(5);

        $factura = new Factura($odv, true);

        //TODO: qutar el mock cuando sea produccion
        if( ENVIROMENT_NAME == 'sandbox') {
            $factura->setTestRFC();
        }

        $save->generateFacturaFromTimone($factura);
    }
}