<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.notifications');

class IntegradoController extends JControllerLegacy {
    //Revisa si el usaurio existe dado un correo electronico
    protected $integradoId;
    protected $app;

    function __construct() {
	    $this->app = JFactory::getApplication();
	    $currUser	= JFactory::getUser();

	    if($currUser->guest){
		    $this->app->redirect('index.php?option=com_users&view=login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
	    }

        $this->sesion = JFactory::getSession();
        $this->integradoId = $this->sesion->get('integradoId', null, 'integrado');

        $this->document = JFactory::getDocument();
        $this->input = $this->app->input;

        parent::__construct();
    }

    public function search_rfc_solicitud() {
        $data = $this->input->getArray( array( 'integradoId' => 'INT', 'rfc' => 'STRING' ) );

        $respuesta = $this->rfc_type($data['rfc']);

        $ex = $this->search_rfc_exists( $data['rfc'] );
        if ( is_numeric($respuesta) && isset($ex) ) {
            $respuesta = array('success' => false, 'msg' => JText::_('LBL_RFC_EXISTE'));
        }

        $this->document->setMimeEncoding( 'application/json' );
        echo json_encode( array('busqueda_rfc' => $respuesta) );
    }

    public function search_rfc_cliente() {
        $db = JFactory::getDbo();
        $this->document->setMimeEncoding( 'application/json' );
        $data = $this->input->getArray( array( 'integradoId' => 'INT', 'rfc' => 'STRING' ) );
        $tipo_rfc = $this->rfc_type($data['rfc']);

        $existe = $this->search_rfc_exists( $data['rfc'] );

        if(!empty($existe)){
            // Busca si existe la relacion entre el integrado actual y el resultado de la busqueda
            $relation = getFromTimOne::selectDB('integrado_clientes_proveedor', 'integradoId = '. $db->quote($this->integradoId) .' AND integradoIdCliente = '.$db->quote($existe) );

            $datos = new IntegradoSimple($existe);
            $datos->integrados[0]->success = true;

            $datos->integrados[0]->tipo_alta = isset($relation[0]->tipo_alta) ? $relation[0]->tipo_alta : '';

            echo json_encode($datos->integrados[0]);
        }elseif( is_numeric($tipo_rfc) ){
            $respuesta['success'] = false;
            $respuesta['msg'] = JText::_('MSG_RFC_NO_EXIST');
            $respuesta['bu_rfc'] = $tipo_rfc;

            echo json_encode( $respuesta );
        }else{
            $tipo_rfc['success'] = 'invalid';
            echo json_encode( array('bu_rfc' => $tipo_rfc) );
        }
    }

    public function rfc_type($rfc) {

        $diccionarioFisica = array( 'rfc' => array( 'rfc_fisica' => true, 'required' => true ) );
        $diccionarioMoral  = array( 'rfc' => array( 'rfc_moral' => true, 'required' => true ) );
        $validator         = new validador();
        $is_validFisica    = $validator->procesamiento( array('rfc' => $rfc), $diccionarioFisica );
        $is_validMoral     = $validator->procesamiento( array('rfc' => $rfc), $diccionarioMoral );

        $respuesta = '';

        if ( ! is_array($is_validMoral['rfc']) ) {
            $respuesta = 1;
        } elseif ( ! is_array($is_validFisica['rfc']) ) {
            $respuesta = 2;
        } else {
            $respuesta['success'] = false;
            $respuesta['msg']     = JText::_( 'MSG_RFC_INVALID' );
        }

        return $respuesta;
    }

    /**
     * @param $rfc
     *
     * @return array
     * @internal param $data
     *
     */
    public function search_rfc_exists( $rfc ) {
        $db        = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))->from('#__integrado_datos_personales')->where($db->quoteName('rfc').' = '.$db->quote($rfc));
        $db->setQuery($query);
        $personales = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))->from('#__integrado_datos_empresa')->where($db->quoteName('rfc').' = '.$db->quote($rfc));
        $db->setQuery($query);
        $empresa = $db->loadResult();

        $integradoId = (!is_null($personales)) ? $personales : $empresa;

        return $integradoId;
    }

    function checkUser(){
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $email = $input->get('data', null, 'STRING');

        $respuesta = self::checkData('users', $db->quoteName('email').' = '.$db->quote($email));

        if(!is_null($respuesta)){
            $response = array('success' => true, 'name' => $respuesta[0]->name, 'userId' => $respuesta[0]->id, 'delete' => false);
        }else{
            $response = array('success' => false, 'msg' => 'El usuario no existe');
        }
        $document = JFactory::getDocument();

        $document->setMimeEncoding('application/json');

        echo json_encode($response);
    }

    /**
     * Salva la alta de usuarios a un integrado
     * @throws Exception
     */
    function saveAltaNewUserOfInteg(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $data = $app->input->getArray();

        $columnas	= array('integradoId','user_id', 'integrado_principal', 'integrado_permission_level');
        $update		= array( $db->quoteName('integrado_permission_level').'= '.$db->quote($data['permission_level']));
        $valores	= array($db->quote($this->integradoId), $data['userId'], 0, $data['permission_level']);

        $existe = self::checkData('integrado_users', $db->quoteName('user_id').' = '. (INT)$data['userId'].' AND '.$db->quoteName('integradoId').' = '. $db->quote($data['integrado_id']) );

        if( empty($existe) ){
            self::insertData('integrado_users', $columnas, $valores);
            $data['type']='new';
        }else{
            self::updateData('integrado_users', $update, $db->quoteName('user_id').' = '.$data['userId']);
            $data['type']='edit';
        }

        $this->sendEmail($data['type']);

        $this->app->redirect('index.php?option=com_integrado&view=altausuarios&Itemid=207', false);
    }

    /**
     * elimina la relacion entre el integrado y el usuario dado de alta
     * @throws Exception
     */
    function deleteUser(){
        $db			= JFactory::getDbo();
        $document	= JFactory::getDocument();
        $input		= JFactory::getApplication()->input;

        $user		= $input->getArray();
        $where 		= array($db->quoteName('user_id') . ' = ' . (INT)$user['data']);

        $response 	= self::deleteData('integrado_users', $where);
        $response['delete']	= true;
        $response['id']		= $user['data'];

        $document->setMimeEncoding('application/json');
        JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

        echo json_encode($response);
    }

    /**
     * carga los archivos y guarda en la base las url donde estan guardadas, al final hace una redirección.
     * @throws Exception
     */
    function uploadFiles(){
        $app = JFactory::getApplication();
        $data = $this->input->getArray();
        $saveFiles = sendToTimOne::uploadFiles($data['integradoId']);

        $msg = $saveFiles ? array('msg' => JText::_('LBL_SAVE_SUCCESSFUL'), 'type' => 'notice') : array('msg' => JText::_('LBL_SAVE_FAILED'), 'type' => 'error');

        if($this->integradoId == ''){
            $url = 'index.php?option=com_integrado&view=solicitud&Itemid=207';
        }else{
            $url = 'index.php?option=com_integrado';
        }

        $app->enqueueMessage($msg['msg'], $msg['type']);
        $app->redirect($url, false);

    }

    /**
     * Recibe el post y lo envia a procesar y guardar
     * @return bool
     * @throws Exception
     */
    function saveform(){
        $data = $this->input->getArray( array( 'integradoId' => 'INT', 'busqueda_rfc' => 'STRING' ) );

        if($data['busqueda_rfc']) {
            $respuesta = $this->rfc_type($data['busqueda_rfc']);
            $ex = $this->search_rfc_exists( $data['busqueda_rfc'] );
        }

        if ( isset( $respuesta ) ) {
            if ( is_array($respuesta) || isset($ex) ) {
                if (isset($ex)) {
                    $respuesta = array('success' => false, 'msg' => JText::_('LBL_RFC_EXISTE'));
                }
                echo json_encode($respuesta);
                return true;
            }
        }

        if (JSession::checkToken() === false) {
            $response = array('success' => false, 'msg'=>'Token Invalido' );
            echo json_encode($response);
            return true;
        }

        $input 	= JFactory::getApplication()->input;

        switch ($input->get('tab', null, 'STRING')) {
            case 'juridica':
                $arrayPost = array(
                    'pj_pers_juridica' => 'STRING',
                    'busqueda_rfc'     => 'STRING',
                );
                break;
            case 'personales':
                $arrayPost = array(
                    'dp_nacionalidad'             => 'STRING',
                    'dp_sexo'                     => 'STRING',
                    'dp_rfc'                      => 'STRING',
                    'dp_calle'                    => 'STRING',
                    'dp_num_exterior'             => 'STRING',
                    'dp_num_interior'             => 'STRING',
                    'dp_cod_postal'               => 'STRING',
                    'dp_tel_fijo'                 => 'STRING',
                    'dp_tel_fijo_extension'       => 'STRING',
                    'dp_tel_movil'                => 'STRING',
                    'dp_email'                    => 'STRING',
                    'dp_nom_comercial'            => 'STRING',
                    'dp_curp'                     => 'STRING',
                    'dp_fecha_nacimiento'         => 'STRING',
                    'dp_nombre_representante'     => 'STRING',
                );
                break;
            case 'empresa':
                $arrayPost = array(
                    'de_razon_social'             => 'STRING',
                    'de_rfc'                      => 'STRING',
                    'de_calle'                    => 'STRING',
                    'de_num_exterior'             => 'STRING',
                    'de_num_interior'             => 'STRING',
                    'de_cod_postal'               => 'STRING',
                    'de_tel_fijo'                 => 'STRING',
                    'de_tel_fijo_extension'       => 'STRING',
                    'de_tel_fax'                  => 'STRING',
                    'de_sitio_web'                => 'STRING',
                    't1_instrum_notaria'          => 'STRING',
                    't1_instrum_estado'           => 'STRING',
                    't1_instrum_nom_notario'      => 'STRING',
                    't1_instrum_num_instrumento'  => 'STRING',
                    't2_instrum_notaria'          => 'STRING',
                    't2_instrum_estado'           => 'STRING',
                    't2_instrum_nom_notario'      => 'STRING',
                    't2_instrum_num_instrumento'  => 'STRING',
                    'pn_instrum_notaria'          => 'STRING',
                    'pn_instrum_estado'           => 'STRING',
                    'pn_instrum_nom_notario'      => 'STRING',
                    'pn_instrum_num_instrumento'  => 'STRING',
                    'rp_instrum_num_instrumento'  => 'STRING',
                    'rp_instrum_estado'           => 'STRING',
                    't1_instrum_fecha'            => 'STRING',
                    't2_instrum_fecha'            => 'STRING',
                    'pn_instrum_fecha'            => 'STRING',
                    'rp_instrum_fecha'            => 'STRING',
                );
                break;
            case 'params':
                $arrayPost = array(
                    'au_params'                   => 'STRING',
                );
                break;
            case 'bancos':
                $arrayPost = array(
                    'db_banco_codigo'             => 'STRING',
                    'db_banco_cuenta'             => 'STRING',
                    'db_banco_sucursal'           => 'STRING',
                    'db_banco_clabe'              => 'STRING',
                );
                break;
        }
        $arrayDefaults = array(
            'tab'         => 'STRING',
            'integradoId' => 'STRING'
        );

        $post 	= $input->getArray( array_merge($arrayPost, $arrayDefaults) );

        $post['integradoId'] = $this->integradoId;

        //Se envia el post para manejar la data y realizar el guardado de esta en la base de datos.
        $response = self::manejoDatos($post);

        if($response['safeComplete'] == true && $input->get('tab', null, 'STRING')== 'personales'){
            $this->sendEmail();
        }
        // Get the document object.
        $document = JFactory::getDocument();
        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');
        // Change the suggested filename.
        JResponse::setHeader('Content-Disposition','attachment; filename="result.json"');

        echo json_encode($response);
    }

    function sepomex(){
        $input = JFactory:: getApplication()->input;
        $cp = $input->getArray(array('cp'=>'STRING'));

        $url = SEPOMEX_SERVICE.$cp['cp'];

        echo file_get_contents($url);
    }

    public static function manejoDatos($data){
        $db	= JFactory::getDbo();
        $integrado_id = is_null($data['integradoId']) ? null : $data['integradoId'];

        switch ($data['tab']) {
            case 'juridica':
                $diccionario = array(
                    'pj_pers_juridica'           => array('number' => true,     'required' => true, 'maxlength' => 1),
                );
                break;

            case 'personales':
                $diccionario = array(
                    'dp_tel_fijo'                => array('phone'       => true,       	'maxlength' => 10,  'minlength'=>10,    'required' => true),
                    'dp_tel_movil'               => array('number'      => true,   	    'maxlength' => 13,  'minlength'=>13,    'required' => true),
                    'dp_nacionalidad'            => array('alphaNumber' => true,      	'maxlength' => 45,  'required' => true),
                    'dp_sexo'                    => array('alphaNumber' => true,	   	'maxlength' => 45,  'required' => true),
                    'dp_rfc'                     => array('rfc_fisica'  => true,      	'maxlength' => 13,  'required' => true),
                    'dp_calle'                   => array('alphaNumber' => true,	    'maxlength' => 100, 'required' => true),
                    'dp_num_exterior'            => array('alphaNumber' => true,        'maxlength' => 20,  'required' => true),
                    'dp_cod_postal'              => array('number' => true,		        'maxlength' => 13,  'required' => true),
                    'dp_email'                   => array('email' => true,		    	'maxlength' => 100, 'required' => true),
                    'dp_nombre_representante'    => array('string' => true,     	    'maxlength' => 150, 'required' => true),
                    'dp_fecha_nacimiento'        => array('date' => true,	        	'maxlength' => 10,  'required' => true),
                    'dp_curp'                    => array('alphaNumber' => true,	    'maxlength' => 18,  'required' => true),
                    'dp_num_interior'            => array('alphaNumber' => true,        'maxlength' => 10),
                    'dp_tel_fijo_extension'      => array('alphaNumber' => true,	    'maxlength' => 5),
                    'dp_nom_comercial'           => array('alphaNumber' => true,  	    'maxlength' => 150),
                );
                break;

            case 'empresa':
                $diccionario = array(
                    'de_razon_social'            => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    'de_rfc'                     => array('rfc_moral' => true,	    	'maxlength' => 12,      'required' => true),
                    'de_calle'                   => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    'de_num_exterior'            => array('alphaNumber' => true,	    'maxlength' => 20,      'required' => true),
                    'de_cod_postal'              => array('alphaNumber' => true,	    'maxlength' => 45,      'required' => true),
                    'de_tel_fijo'                => array('alphaNumber' => true,        'maxlength' => 10,      'required' => true),
                    'de_tel_fijo_extension'      => array('alphaNumber' => true,        'maxlength' => 10,      ),
                    'de_tel_fax'                 => array('alphaNumber' => true,        'maxlength' => 10,      ),
                    'de_sitio_web'               => array(/*'string'      => true*/     'maxlength' => 150,     ),
                    't1_instrum_fecha'           => array('date'        => true,       	'maxlength' => 10,      'required' => true),
                    't1_instrum_notaria'         => array('alphaNumber' => true,	    'maxlength' => 45,      'required' => true),
                    't1_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 5,       'required' => true),
                    't1_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     'required' => true),
                    't1_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      'required' => true),
                    'de_num_interior'            => array('alphaNumber' => true,	    'maxlength' => 5,       ),
                    't2_instrum_fecha'           => array('date'        => true,       	'maxlength' => 10,      ),
                    't2_instrum_notaria'         => array('alphaNumber' => true,	    'maxlength' => 13,      ),
                    't2_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    't2_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    't2_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 18,      ),
                    'pn_instrum_fecha'           => array('date'        => true,      	'maxlength' => 10,      ),
                    'pn_instrum_notaria'         => array('alphaNumber' => true, 	    'maxlength' => 18,      ),
                    'pn_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 255,     ),
                    'pn_instrum_nom_notario'     => array('alphaNumber' => true,	    'maxlength' => 100,     ),
                    'pn_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                    'rp_instrum_fecha'           => array('date'        => true,      	'maxlength' => 10,      ),
                    'rp_instrum_num_instrumento' => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                    'rp_instrum_estado'          => array('alphaNumber' => true,	    'maxlength' => 10,      ),
                );
                break;

            case 'banco':
                $diccionario = array(
                    'db_banco_clabe'             => array('banco_clabe' => $data['db_banco_codigo'],    'maxlength' => 18,  'minlength'=>18,   'required' => true),
                    'db_banco_cuenta'            => array('alphaNumber' => true,                        'maxlength' => 11,  'minlength'=>11,   'required' => true),
                    'db_banco_codigo'            => array('alphaNumber' => true,	                    'maxlength' => 3,   'required' => true),
                    'db_banco_sucursal'          => array('alphaNumber' => true,	                    'maxlength' => 10,  'required' => true),
                );
                break;
        }

        $diccionarioDefault  = array(
            'integradoId'                => array('alohaNum' => true,   'maxlength' => 36),
            'tab'                        => array('string' => true,     'maxlength' => 18),
        );
        $diccionario = array_merge($diccionario, $diccionarioDefault);

        if($integrado_id === null){
            $integrado_id = getFromTimOne::newintegradoId($data['pj_pers_juridica']);

            $columnas 		= array('user_id', 'integradoId', 'integrado_principal', 'integrado_permission_level');
            $valores 		= array(JFactory::getUser()->id, $db->quote($integrado_id), 1, 3);

            $saveUserIntegRealtion = self::insertData('integrado_users', $columnas, $valores);

            $session = JFactory::getSession();
            $session->set('integradoId',$integrado_id,'integrado');

            $data['integradoId'] = $integrado_id;
        }

        //envia la data a validacion y regresa un arreglo con los resultados para cada uno de los campos que esten llenados
        $validador = new validador();
        $resultado = $validador->procesamiento($data, $diccionario);

        foreach ($resultado as $key => $value) {
            if(is_array($value) ){
                $resultado['safeComplete']  = false;
                $resultado['integradoId']   = $integrado_id;
                return $resultado;
            }
        }

        switch($data['tab']){
            case 'juridica':
                $createdDate  = time();
                $table 		  = 'integrado';
                $columnas 	  = array('integradoId','status','pers_juridica', 'createdDate');
                $valores	  = array( $db->quote($integrado_id), '0', $data['pj_pers_juridica'], $createdDate );
                $updateSet 	  = array($db->quoteName('pers_juridica').' = '.$data['pj_pers_juridica'] );
                break;
            case 'personales':
                $table 		= 'integrado_datos_personales';
                $columnas[] = 'integradoId';
                $valores[]	= $db->quote($integrado_id);
                $valoresvalidaicon['integradoId']= $integrado_id;

                foreach ($data as $key => $value) {
                    $columna 	= substr($key, 3);
                    $clave 		= substr($key, 0,3);

                    if($clave == 'dp_'){
                        $columnas[]					 	= $columna;
                        $valores[] 						= $db->quote($value);
                        $updateSet[]					= $db->quoteName($columna).' = '.$db->quote($value);
                        $valoresvalidaicon[$columna] 	= $value;
                    }
                }
                break;
            case 'empresa':
                $table = 'integrado_datos_empresa';
                $columnas[] = 'integradoId';
                $valores[]	= $db->quote($integrado_id);

                self::saveInstrumentos($data);

                foreach ($data as $key => $value) {
                    $columna 	= substr($key, 3);
                    $clave 		= substr($key, 0,3);

                    if($clave == 'de_'){
                        $columnas[] 	= $columna;
                        $valores[] 		= $db->quote($value);
                        $updateSet[]	= $db->quoteName($columna).' = '.$db->quote($value);
                        $valoresvalidaicon[$columna] = $value;
                    }
                }

                break;
            case 'bancos':
                $table = 'integrado_datos_bancarios';
                $columnas[] = 'integradoId';
                $valores[]	= $db->quote($integrado_id);

                foreach ($data as $key => $value) {
                    $columna 	= substr($key, 3);
                    $clave 		= substr($key, 0,3);

                    if($clave == 'db_'){
                        $columnas[] = $columna;
                        $valores[] = $db->quote($value);
                        $updateSet[]	= $db->quoteName($columna).' = '.$db->quote($value);
                        $valoresvalidaicon[$columna] = $value;
                    }
                }
                break;
        }

        $existe = self::checkData($table, $db->quoteName('integradoId').' = '. $db->quote($integrado_id) );

        if( empty($existe) ){
            $respuesta = self::insertData($table, $columnas, $valores);
        }else{
            $condicion 	= array($db->quoteName('integradoId').' = '. $db->quote($integrado_id) );
            $respuesta = self::updateData($table, $updateSet, $condicion);
        }

        $resultado['safeComplete']  = true;
        $resultado['integradoId'] = $integrado_id;

        return $resultado;
    }

    public static function checkData($table, $where){
        $results = false;
        try{
            $db		= JFactory::getDbo();
            $query 	= $db->getQuery(true);

            $query->select('*')
                ->from($db->quoteName('#__'.$table))
                ->where($where);

            $db->setQuery($query);

            $results = $db->loadObjectList();
        }
        catch(Exception $e){
            $response = array('success' => false , 'msg' => 'Error en checkData');
            echo json_encode($response);
        }

        return $results;
    }

    public static function insertData($tabla, $columnas, $valores){
        try{
            $db		= JFactory::getDbo();
            $query 	= $db->getQuery(true);

            $query->insert($db->quoteName('#__'.$tabla))
                ->columns($db->quoteName($columnas))
                ->values(implode(',',$valores));

            $db->setQuery($query);
            $db->execute();

            return array('success' => true , 'msg' => 'Datos Almacenados correctamente');

        }
        catch(Exception $e){
            $response = array('success' => false , 'msg' => 'Error al guardar intente nuevamente');
            echo json_encode($response);
        }
    }

    public static function updateData($table, $columnas, $condicion){
        try{
            $db		= JFactory::getDbo();
            $query 	= $db->getQuery(true);

            $query->update($db->quoteName('#__'.$table))
                ->set(implode(',', $columnas))
                ->where($condicion);

            $db->setQuery($query);
            $db->execute();

            return array('success' => true , 'msg' => 'Datos Actualizados correctamente');
        }
        catch(Exception $e){
            $response = array('success' => false , 'msg' => 'Error al Actualizar intente nuevamente');
            echo json_encode($response);
        }
    }

    public static function deleteData($table, $where){
        try{
            $db		= JFactory::getDbo();
            $query	= $db->getQuery(true);

            $query->delete($db->quoteName('#__'.$table));
            $query->where($where);

            $db->setQuery($query);
            $db->execute();

            return array('success' => true , 'msg' => 'Datos Actualizados correctamente');
        }
        catch(Exception $e){
            return array('success' => false , 'msg' => 'Error al eliminar el usuario');
        }
    }

    public static function saveInstrumentos($data){
        $db				= JFactory::getDbo();
        $columnast1[] 	= 'integradoId';
        $columnast2[] 	= 'integradoId';
        $columnasPN[] 	= 'integradoId';
        $columnasRP[]	= 'integradoId';
        $columnast1[] 	= 'instrum_type';
        $columnast2[] 	= 'instrum_type';
        $columnasPN[] 	= 'instrum_type';
        $columnasRP[]	= 'instrum_type';

        $valort1[]		= $db->quote($data['integradoId']);
        $valort2[]		= $db->quote($data['integradoId']);
        $valorPN[]		= $db->quote($data['integradoId']);
        $valorRP[]		= $db->quote($data['integradoId']);
        $valort1[]		= 1;
        $valort2[]		= 2;
        $valorPN[]		= 3;
        $valorRP[]		= 4;

        foreach ($data as $key => $value) {
            $columna 	= substr($key, 3);
            $clave 		= substr($key, 0,3);

            switch ($clave) {
                case 't1_':
                    $columnast1[] 	= $columna;
                    $valort1[]		= $db->quote($value);
                    $updateSett1[]	= $db->quoteName($columna).' = '.$db->quote($value);
                    break;
                case 't2_':
                    $columnast2[] 	= $columna;
                    $valort2[]		= $db->quote($value);
                    $updateSett2[]	= $db->quoteName($columna).' = '.$db->quote($value);
                    break;
                case 'pn_':
                    $columnasPN[] 	= $columna;
                    $valorPN[]		= $db->quote($value);
                    $updateSetpn[]	= $db->quoteName($columna).' = '.$db->quote($value);
                    break;
                case 'rp_':
                    $columnasRP[] 	= $columna;
                    $valorRP[]		= $db->quote($value);
                    $updateSetrp[]	= $db->quoteName($columna).' = '.$db->quote($value);
                    break;
                default:

                    break;
            }
        }

        $where = $db->quoteName('integradoId').' = '. $db->quote($data['integradoId']) .' AND '.$db->quoteName('instrum_type').' = 1';
        $existet1 = self::checkData('integrado_instrumentos', $where);
        if(empty($existet1) ){
            self::insertData('integrado_instrumentos', $columnast1, $valort1);
            $existet1 = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSett1, $where);
            $existet1 = self::checkData('integrado_instrumentos', $where);
        }
        if (!empty($existet1)) {
            self::saveInstrumentosEmpresa($data['integradoId'], $existet1[0]->id, 'testimonio_1');
        }

        $where = $db->quoteName('integradoId').' = '. $db->quote($data['integradoId']) .' AND '.$db->quoteName('instrum_type').' = 2';
        $existet2 = self::checkData('integrado_instrumentos', $where);
        if(empty($existet2) ){
            self::insertData('integrado_instrumentos', $columnast2, $valort2);
            $existet2 = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSett2, $where);
            $existet2 = self::checkData('integrado_instrumentos', $where);
        }

        if (!empty($existet2)) {
            self::saveInstrumentosEmpresa($data['integradoId'], $existet2[0]->id, 'testimonio_2');
        }

        $where = $db->quoteName('integradoId').' = '. $db->quote($data['integradoId']) .' AND '.$db->quoteName('instrum_type').' = 3';
        $existepn = self::checkData('integrado_instrumentos', $where);
        if(empty($existepn) ){
            self::insertData('integrado_instrumentos', $columnasPN, $valorPN);
            $existepn = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSetpn, $where);
            $existepn = self::checkData('integrado_instrumentos', $where);
        }
        if (!empty($existepn)) {
            self::saveInstrumentosEmpresa($data['integradoId'], $existepn[0]->id, 'poder');
        }

        $where = $db->quoteName('integradoId').' = '. $db->quote($data['integradoId']) .' AND '.$db->quoteName('instrum_type').' = 4';
        $existerp = self::checkData('integrado_instrumentos', $where);
        if(empty($existerp) ){
            self::insertData('integrado_instrumentos', $columnasRP, $valorRP);
            $existerp = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSetrp, $where);
            $existerp = self::checkData('integrado_instrumentos', $where);
        }
        if (!empty($existerp[0])) {
            self::saveInstrumentosEmpresa($data['integradoId'], $existerp[0]->id, 'reg_propiedad');
        }
    }

    public static function saveInstrumentosEmpresa($integrado_id, $id_instrumento, $campo){
        $db				= JFactory::getDbo();
        $where			= $db->quoteName('integradoId').' = '. $db->quote($integrado_id) ;
        $dataEmpresa 	= self::checkData('integrado_datos_empresa', $where);
        $columna[] 		= $campo;
        $columna[]		= 'integradoId';
        $valor[]		= $id_instrumento;
        $valor[]		= $db->quote($integrado_id);
        $updateSet[] 	= $db->quoteName($campo).' = '.$db->quote($id_instrumento);

        if(empty($dataEmpresa)){
            self::insertData('integrado_datos_empresa', $columna, $valor);
        }else{
            self::updateData('integrado_datos_empresa', $updateSet, $where);
        }
    }

    public function createNewSolicitud() {
        $this->sesion->clear('integradoId', 'integrado');
        JFactory::getApplication()->redirect('index.php?option=com_integrado&view=solicitud&Itemid=207');
    }

    public function select() {
        $this->app = JFactory::getApplication();

        if (!JSession::checkToken()) {
            $this->app->enqueueMessage( JText::_( 'LBL_ERROR' ), 'error' );
            $this->redirectToSelectIntegrado();
        } else {
            $this->id = $this->app->input->get('integradoId', null, 'STRING');

            try {
                $integrado = $this->getIntegradoForUserByRequestId();
                $integradoSimple = new IntegradoSimple($integrado->id);
                Integrado::setIntegradoInSession($integradoSimple);

                $this->app->redirect( 'index.php?option=com_mandatos');

            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->redirectToSelectIntegrado();
            }
        }

    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getIntegradoForUserByRequestId() {
        $model = $this->getModel();
        $integrados = $model->getIntegrados();

        if (!array_key_exists($this->id, $integrados)) {
            throw new Exception(JText::_('ERROR_'));
        }

        return $integrados[$this->id];
    }

    public function redirectToSelectIntegrado() {
        $this->app->redirect( 'index.php?option=com_integrado&view=integrado&layout=change&Itemid=207');
    }

    public function agregarBancoSolicitud() {
        $sesion = JFactory::getSession();
        $integradoId = $sesion->get('integradoId', null, 'integrado');

        list( $respuesta, $existe, $newId, $db, $data, $save ) = Integrado::saveBankIfNew( $integradoId );
        $this->document->setMimeEncoding('application/json');
        echo json_encode($respuesta);

    }

    public function sendEmail($type=null)
    {
    /*
	 *  NOTIFICACIONES 0 & 1
	 */
	    $getCurrUser = new IntegradoSimple($this->integradoId);

	    if(is_null($type)){
		    $array = array(
			    $getCurrUser->user->name,
			    $this->integradoId,
			    date('d-m-Y'));
		    $noEmail = 1;
		    $typeAlta = '';
            $titleArray = array();
	    }else{
		    if($type == 'edit'){
			    $typeAlta = 'Edicion';
		    }
		    if($type == 'new'){
			    $typeAlta = 'Alta';
		    }
		    foreach ($getCurrUser->usuarios as $key => $value) {
			    if($value->id == $_POST['userId']){
				    $dataUser = $value;
			    }
		    }
		    $titleArray = array ( $typeAlta );
		    $array = array(
			    $typeAlta,
			    $dataUser->email,
			    $dataUser->username,
			    $this->getPermisoString(),
			    date('d-m-Y'));
		    $noEmail = 0;
	    }
	    $send = new Send_email();
	    $send->setIntegradoEmailsArray($getCurrUser);
	    $info = $send->sendNotifications($noEmail, $array, $titleArray);
	    return $info;
    }

    public function finish( ){
        $db = JFactory::getDbo();
        if ( isset( $this->integradoId ) ) {
            $integrado = new IntegradoSimple($this->integradoId);

            if ( $integrado->hasAllDataForValidation() ) {
                $this->app->enqueueMessage( JText::_('LBL_DATA_VALIDATION_INTEGRADO_COMPLETE') );
                $integrado->integrados[0]->integrado->status = 1;
                $db->updateObject('#__integrado',$integrado->integrados[0]->integrado,'integradoId');

                $notifications = new Send_email();
                $notifications->setIntegradoEmailsArray($integrado);
                $notifications->sendNotifications( 40, array( date('d-m-Y') ) );
                $notificationAdmin = new Send_email();
                $notificationAdmin->setAdminEmails();
                $notifications->sendNotifications( 41, array( $integrado->getDisplayName(),date('d-m-Y') ) );

            } else {
                $this->app->enqueueMessage( JText::_('LBL_DATA_VALIDATION_INTEGRADO_MISSING') );
            }
        }

        $this->app->redirect(JRoute::_('index.php?option=com_integrado'));
    }

    function deleteBanco(){
        $input_data = JFactory::getApplication()->input;
        $db	        = JFactory::getDbo();
        $save       = new sendToTimOne();
        $post       = array('datosBan_id' => 'INT');
        $data 		= $input_data->getArray($post);
        $table 		= 'integrado_datos_bancarios';
        $where      = $db->quoteName('integradoId').' = '.$db->quote($this->integradoId).' && '.$db->quoteName('datosBan_id').' = '. (INT)$data['datosBan_id'];

        $respuesta['msg'] = $save->deleteDB($table,$where);

        if($respuesta['msg']) {
            $respuesta['success'] = true;
        }else{
            $respuesta['success'] = false;
        }

        $this->document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    function getPermisoString() {
        $permiso = '';
        if ( isset( $_POST['permission_level'] ) ) {
            switch ($_POST['permission_level']){
                case 1:
                    $permiso = 'Consulta';
                    break;
                case 2:
                    $permiso = 'Operaciones';
                    break;
                case 3:
                    $permiso = 'Autorizador';
                    break;
                case 4:
                    $permiso = 'Full';
                    break;
            }
        }
        return $permiso;
    }

}