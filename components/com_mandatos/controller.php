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

    public function __construct(){
        parent::__construct();
        $this->app			= JFactory::getApplication();
        $this->document     = JFactory::getDocument();
        $this->currUser	 	= JFactory::getUser();
        $this->input_data	= $this->app->input;
        $data		 		= $this->input_data->getArray();

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

//        $integradoId 		= isset($integrado->integrados[0]) ? $integrado->integrados[0]->integrado_id : $this->integradoId;

        // $isValid 	 		= $integrado->isValidPrincipal($integradoId, $this->currUser->id);

        if($this->currUser->guest){
            $this->app->redirect('index.php?option=com_users&view=login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
        }
        if(is_null($this->integradoId)){
            $this->app->redirect('index.php?option=com_integrado&view=integrado', JText::_('MSG_REDIRECT_INTEGRADO'), 'Warning');
        }
        else {
            $integrado	    = new IntegradoSimple($this->integradoId);
            $canOperate     = $integrado->canOperate();
            if(!$canOperate){
                $this->app->redirect('index.php?option=com_integrado&view=integrado', JText::_('MSG_REDIRECT_INTEGRADO_CANT_OPERATE'), 'Warning');
            }
        }
    }

    function editarproyecto(){
        $post           = array('integradoId'=>'INT', 'id_proyecto'=>'INT');
        $data 			= $this->input_data->getArray($post);
        $proyectos 		= getFromTimOne::getProyects($this->integradoId);
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

    function agregarBanco(){
        list( $respuesta, $existe, $newId, $db, $data, $save ) = $this->saveBankIfNew();

        $idClipro = isset($newId) ? $newId : $existe[0]->datosBan_id;
        $respuesta['datosBan_id'] = $idClipro;

        $table 		= 'integrado_datos_bancarios';
        $where      = $db->quoteName('banco_clabe').' = '.$data['db_banco_clabe'];
        $existe     = getFromTimOne::selectDB($table,$where);
        $existe     = $existe[0];

        // se busca la relacion del cliente y el integrado
        $tableRelacion 		= 'integrado_clientes_proveedor';
        $whereRelacion      = $db->quoteName('integrado_id').' = '.$this->integradoId.' && '.$db->quoteName('integradoIdCliente').' = '.$data['integradoId'];
        $relacion           = getFromTimOne::selectDB($tableRelacion,$whereRelacion);
        if ( ! empty( $relacion[0] ) ) {
            $relacion           = $relacion[0];
        }

        // Si no existe la relacion la creamos
        $bancos = isset($relacion->bancos) ? json_decode($relacion->bancos, true) : array();

        if( !in_array( $existe->datosBan_id, $bancos) && $this->integradoId != $data['integradoId'] ) {
            array_push($bancos, $existe->datosBan_id);

            $datos   = array('bancos' => json_encode($bancos));

            $save->formatData($datos);
            $where = $db->quoteName('integrado_id').' = '.$this->integradoId.' && '.$db->quoteName('integradoIdCliente').' = '.$data['integradoId'];
            $update = $save->updateDB('integrado_clientes_proveedor', null, $where );

            $idClipro = isset($newId) ? $newId : $existe->datosBan_id;
            $respuesta['success'] = $update;
            $respuesta['banco_codigo'] = $data['db_banco_codigo'];
            $respuesta['banco_cuenta'] = $data['db_banco_cuenta'];
            $respuesta['banco_sucursal'] = $data['db_banco_sucursal'];
            $respuesta['banco_clabe'] = $data['db_banco_clabe'];
            $respuesta['datosBan_id'] = $idClipro;

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
        $where      = $db->quoteName('integrado_id').' = '.$this->integradoId.' && '.$db->quoteName('datosBan_id').' = '.$data['datosBan_id'];

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

    function saveCliPro(){
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
            'pn_instrum_estado'             => 'STRING',
            'pn_instrum_nom_notario'        => 'STRING',
            'pn_instrum_notaria'            => 'STRING',
            'pn_instrum_num_instrumento'    => 'STRING',
            'rp_instrum_estado'             => 'STRING',
            'rp_instrum_num_instrumento'    => 'STRING',
            't1_instrum_estado'             => 'STRING',
            't1_instrum_nom_notario'        => 'STRING',
            't1_instrum_notaria'            => 'STRING',
            't1_instrum_num_instrumento'    => 'STRING',
            't2_instrum_estado'             => 'STRING',
            't2_instrum_nom_notario'        => 'STRING',
            't2_instrum_notaria'            => 'STRING',
            't2_instrum_num_instrumento'    => 'STRING',
            'tab'                           => 'STRING',
            'tp_tipo_alta'                  => 'INT',
            'tp_status'                     => 'INT',
            'tp_monto'                      => 'FLOAT');

        $tab        = 'tipo_alta';
        $data       = $this->input_data->getArray($arrayPost);
        $data['integradoId'] = $this->integradoId;
        $idCliPro   = $data['idCliPro'];
        $datosQuery['setUpdate'] = array();

        $this->dataCliente  = (object) $data;


        // verificación que no sea el mismo integrado
        $currentIntegrado = new IntegradoSimple($this->integradoId);

        if($idCliPro == 0){
            $idCliPro = getFromTimOne::newintegradoId($data['pj_pers_juridica']);
            $data['idCliPro'] = $idCliPro;
        }

        switch($data['tab']){
            case 'tipoAlta':
                $table 		= 'integrado_clientes_proveedor';
                $where      = $db->quoteName('integradoIdCliente').' = '.$idCliPro.' && integrado_Id = '.$this->integradoId;
                $existe     = getFromTimOne::selectDB($table,$where);

                $columnas[] = 'integrado_id';
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
                $where      = $db->quoteName('integrado_Id').' = '.$idCliPro;
                $existe     = getFromTimOne::selectDB($table, $where);

                $datosQuery['setUpdate'] = array($db->quoteName('pers_juridica').' = '.$db->quote($data['pj_pers_juridica']));
                $tab = 'basic-details';
                break;
            case 'personales':
                $table 		= 'integrado_datos_personales';
                $where      = $db->quoteName('integrado_Id').' = '.$idCliPro;
                $existe     = getFromTimOne::selectDB($table, $where);

                $columnas[] = 'integrado_id';
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
                $where = $db->quoteName('integrado_id').' = '.$idCliPro;
                $existe = getFromTimOne::selectDB($table, $where);
                $columnas[] = 'integrado_id';
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

                $this->sendEmail();

            }

        }else{
            $save->updateDB($table,$datosQuery['setUpdate'],$where);
        }

        $response['idCliPro'] = $idCliPro;
        $response['success'] = true;
        $response['nextTab'] = $tab;

        $this->document->setMimeEncoding('application/json');

        echo json_encode($response);
    }


    //carga los archivos y guarda en la base las url donde estan guardadas, al final hace una redirección.
    function uploadFiles(){

        $idCliPro	= JFactory::getApplication()->input->get('idCliPro', null, 'INT');
        $result     = getFromTimOne::selectDB('integrado_clientes_proveedor', 'id = '.$idCliPro);
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
        $where      = $db->quoteName('integrado_id').' = '.$integradoId;
        $existe     = getFromTimOne::selectDB($table, $where);

        $columnas[] = 'integrado_id';
        $valores[]	= $integradoId;

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

            $columnas[] = 'integrado_id';
            $columnas[] = 'telefono';
            $columnas[] = 'movil';
            $columnas[] = 'ext';
            $columnas[] = 'correo';

            $tel = 'tel_fijo'.($i+1);
            $movil = 'tel_movil'.($i+1);
            $ext = 'tel_fijo_extension'.($i+1);
            $correo = 'email'.($i+1);

            $valores[] = $integradoId;
            $valores[] = $arreglo[$tel];
            $valores[] = $arreglo[$movil];
            $valores[] = $arreglo[$ext];
            $valores[] = $arreglo[$correo];

            $save->insertDB('integrado_contacto',$columnas,$valores);
        }
    }

    function searchProducts(){
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        $catalogos = new Catalogos();
        $catalogoIva = $catalogos->getCatalogoIVA();

        $respuesta = array();
        $db     = JFactory::getDbo();
        $campos = array('productName'=>'STRING');
        $data   = $this->input_data->getArray($campos);

        $where  = $db->quoteName('productName').' = '.$db->quote($data['productName']).' AND '.$db->quoteName('integradoId').' = '.$this->integradoId.' AND '.$db->quoteName('status').' = 1';

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

    public function envioTimOne($envio)
    {
        $request = new sendToTimOne();
        $serviceUrl = new IntRoute();

        $request->setServiceUrl($serviceUrl->saveComisionServiceUrl());
        $request->setJsonData($envio);

        $respuesta = $request->to_timone(); // realiza el envio

        return $respuesta;
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
                $where = 'id_proyecto = '.$data['id'];
                break;
            case 'producto':
                $table = 'integrado_products';
                $where = 'id_producto = '.$data['id'];
                break;
            case 'cliente';
                $table = 'integrado_clientes_proveedor';
                $where = 'id = '.$data['id'];
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

    public function tabla(){
        $this->document->setMimeEncoding('application/json');
        $input  = $this->input;
        $post   = array(
            'tiempoplazo' => 'FLOAT',
            'tipoPlazo'   => 'FLOAT',
            'capital'     => 'FLOAT',
            'interes'     => 'FLOAT'
        );
        $data   = (object) $input->getArray($post);

        $tabla = getFromTimOne::getTablaAmotizacion($data);
        echo json_encode($tabla);

    }

    public function getFacturasCom(){
        $integradoId = $this->integradoId;

        $factura = new facturasComision();

        $test = $factura->getFacturaComision($integradoId);
        exit;
    }

    public function sendEmail()
    {
        /*
         * Notificaciones 5
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

        $getCurrUser = new IntegradoSimple($this->integradoId);

        $array = array($getCurrUser->user->name, $tipo, $this->dataCliente->dp_nom_comercial, JFactory::getUser()->name, date('d-m-Y'));

        $sendEmail = new Send_email();
        $sendEmail->setIntegradoEmailsArray($getCurrUser);

        $infoEmail = $sendEmail->sendNotifications('5', $array);
        return $infoEmail;
    }

//    public function agregarBancoSolicitud() {
//        list( $respuesta, $existe, $newId, $db, $data, $save ) = $this->saveBankIfNew();
//        $this->document->setMimeEncoding('application/json');
//        echo json_encode($respuesta);
//
//    }

    /**
     * @return array
     * @internal param $respuesta
     * @internal param $columnas
     * @internal param $valores
     *
     */
//    public function saveBankIfNew() {
//        $respuesta['success'] = false;
//
//        $db         = JFactory::getDbo();
//        $save       = new sendToTimOne();
//        $datosQuery = array( 'setUpdate' => array() );
//        $post       = array(
//            'integradoId'       => 'INT',
//            'datosBan_id'       => 'INT',
//            'db_banco_codigo'   => 'STRING',
//            'db_banco_cuenta'   => 'STRING',
//            'db_banco_sucursal' => 'STRING',
//            'db_banco_clabe'    => 'STRING',
//        );
//
//        $data = $this->input_data->getArray( $post );
//
//        // busca los datos bancario por la CLABE
//        $table  = 'integrado_datos_bancarios';
//        if ( empty( $data['db_banco_clabe'] ) ) {
//            $data['db_banco_clabe'] = '0000000';
//        }
//        $where  = $db->quoteName( 'banco_clabe' ) . ' = ' . $data['db_banco_clabe'];
//        $existe = getFromTimOne::selectDB( $table, $where );
//
//        $logdata = implode( ', ', array(
//            JFactory::getUser()->id,
//            $this->integradoId,
//            __METHOD__ . ':' . __LINE__,
//            json_encode( $existe )
//        ) );
//        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );
//
//        if ( empty( $existe ) ) {
//            $columnas[] = 'integrado_id';
//            $valores[]  = $this->integradoId;
//
//            $datosQuery['columnas'] = $columnas;
//            $datosQuery['valores']  = $valores;
//
//            $datosQuery = self::limpiarPost( $data, 'db_', $datosQuery );
//
//            $validator   = new validador();
//            $diccionario = array(
//                'db_banco_codigo' => array( 'alphaNumber' => true, 'length' => 3, 'required' => true ),
//                'db_banco_cuenta'=> array( 'required' => true),
//                'db_banco_sucursal'=> array( 'required' => true),
//                'db_banco_clabe' => array( 'banco_clabe' => $data['db_banco_codigo'], 'length'   => 18 )
//            );
//            $validacion  = $validator->procesamiento( $data, $diccionario );
//
//            if ( $validator->allPassed() ) {
//                if ( empty( $existe ) ) {
//                    $save->insertDB( $table, $datosQuery['columnas'], $datosQuery['valores'] );
//                    $newId = $db->insertid();
//                } else {
//                    $save->updateDB( $table, $datosQuery['setUpdate'], $where );
//                }
//
//                $respuesta['success']        = true;
//                $respuesta['banco_codigo']   = $data['db_banco_codigo'];
//                $respuesta['banco_cuenta']   = $data['db_banco_cuenta'];
//                $respuesta['banco_sucursal'] = $data['db_banco_sucursal'];
//                $respuesta['banco_clabe']    = $data['db_banco_clabe'];
//
//                return array($respuesta, $existe, $newId, $db, $data, $save);
//            } else {
//                $logdata = implode( ', ', array(
//                    JFactory::getUser()->id,
//                    $this->integradoId,
//                    __METHOD__ . ':' . __LINE__,
//                    json_encode( array( $validacion, $data['db_banco_clabe'], $data['db_banco_codigo'] ) )
//                ) );
//                JLog::add( $logdata, JLog::DEBUG, 'bitacora' );
//
//                $respuesta['success'] = false;
//                $respuesta['msg'] = $validacion;
//
//                return array($respuesta, $existe, null, $db, $data, $save);
//            }
//        }
//
//        return array($respuesta, $existe, null, $db, $data, $save);
//    }


}