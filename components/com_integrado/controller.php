<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.notifications');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();

if($currUser->guest){
    $app->redirect('index.php/login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
}

class IntegradoController extends JControllerLegacy {
    //Revisa si el usaurio existe dado un correo electronico
    protected $integradoId;
    protected $app;

    function __construct() {
        $this->sesion = JFactory::getSession();
        $this->integradoId = $this->sesion->get('integradoId', null, 'integrado');

        parent::__construct();
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

    //Salva la alta de usuarios a un integrado
    function saveAltaNewUserOfInteg(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $data = $app->input->getArray();

        $columnas	= array('integrado_id','user_id', 'integrado_principal', 'integrado_permission_level');
        $update		= array( $db->quoteName('integrado_permission_level').'= '.$db->quote($data['permission_level']));
        $valores	= array($this->integradoId, $data['userId'], 0, $data['permission_level']);


        $existe = self::checkData('integrado_users', $db->quoteName('user_id').' = '.$data['userId'].' AND '.$db->quoteName('integrado_id').' = '.$data['integrado_id']);

        if( empty($existe) ){
            self::insertData('integrado_users', $columnas, $valores);
        }else{
            self::updateData('integrado_users', $update, $db->quoteName('user_id').' = '.$data['userId']);
        }

        JApplication::redirect('index.php?option=com_integrado&view=altausuarios', false);
    }

    //elimina la relacion entre el integrado y el usuario dado de alta
    function deleteUser(){
        $db			= JFactory::getDbo();
        $document	= JFactory::getDocument();
        $input		= JFactory::getApplication()->input;

        $user		= $input->getArray();
        $where 		= array($db->quoteName('user_id') . ' = ' . $user['data']);

        $response 	= self::deleteData('integrado_users', $where);
        $response['delete']	= true;
        $response['id']		= $user['data'];

        $document->setMimeEncoding('application/json');
        JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

        echo json_encode($response);
    }

    //carga los archivos y guarda en la base las url donde estan guardadas, al final hace una redirección.
    function uploadFiles(){

        sendToTimOne::uploadFiles();

        if($this->integradoId ==''){
            $url = 'index.php?option=com_integrado&view=solicitud';
        }else{
            $url = 'index.php?option=com_integrado&view=solicitud&integradoId='.$this->integradoId;
        }

        JFactory::getApplication()->redirect($url, false);

    }

    //Recibe el post y lo envia a procesar y guardar
    function saveform(){
        if (JSession::checkToken() === false) {
            $response = array('success' => false, 'msg'=>'Token Invalido' );
            echo json_encode($response);
            return true;
        }

        $input 	= JFactory::getApplication()->input;
        $arrayPost =array(
            'user_id'                     => 'STRING',
            'pj_pers_juridica'            => 'STRING',
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
            'de_razon_social'             => 'STRING',
            'dp_nombre_representante'     => 'STRING',
            'de_rfc'                      => 'STRING',
            'de_calle'                    => 'STRING',
            'de_num_exterior'             => 'STRING',
            'de_num_interior'             => 'STRING',
            'de_cod_postal'               => 'STRING',
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
            'au_params'                   => 'STRING',
            'db_banco_codigo'             => 'STRING',
            'db_banco_cuenta'             => 'STRING',
            'db_banco_sucursal'           => 'STRING',
            'db_banco_clabe'              => 'STRING',
            'tab'                         => 'STRING',
            'dp_fecha_nacimiento'         => 'STRING',
            't1_instrum_fecha'            => 'STRING',
            't2_instrum_fecha'            => 'STRING',
            'pn_instrum_fecha'            => 'STRING',
            'rp_instrum_fecha'            => 'STRING',
            'integradoId'                 => 'STRING'
        );

        $post 	= $input->getArray($arrayPost);

        $post['integradoId'] = $this->integradoId;

        //Se envia el post para manejar la data y realizar el guardado de esta en la base de datos.
        $response = self::manejoDatos($post);

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

        $diccionario  = array(
            'user_id'                    => array('number' => true,		    	'length'=>10),
            'integradoId'                => array('number' => true,		    	'length'=>10),
            'pj_pers_juridica'           => array('number' => true,		    	'length'=>10),
            'dp_nacionalidad'            => array('alphaNumber' => true,      	'length'=>45),
            'dp_sexo'                    => array('alphaNumber' => true,	   	'length'=>45),
            'dp_rfc'                     => array('rfc_fisica' => true,      	'length'=>13),
            'dp_calle'                   => array('alphaNumber' => true,	    'length'=>100),
            'dp_num_exterior'            => array('alphaNumber' => true,        'length'=>10),
            'dp_num_interior'            => array('alphaNumber' => true,        'length'=>10),
            'dp_cod_postal'              => array('number' => true,		        'length'=>13),
            'dp_tel_fijo'                => array('phone' => true,            	'length'=>10,   'minlength'=>10),
            'dp_tel_fijo_extension'      => array('alphaNumber' => true,	    'length'=>5),
            'dp_tel_movil'               => array('number' => true,     	    'length'=>13,   'minlength'=>13),
            'dp_email'                   => array('email' => true,		    	'length'=>100),
            'dp_nom_comercial'           => array('alphaNumber' => true,  	    'length'=>150),
            'dp_nombre_representante'    => array('string' => true,     	    'length'=>150),
            'dp_curp'                    => array('alphaNumber' => true,	    'length'=>18),
            'de_razon_social'            => array('alphaNumber' => true,	    'length'=>100),
            'de_rfc'                     => array('rfc_moral' => true,	    	'length'=>12),
            'de_calle'                   => array('alphaNumber' => true,	    'length'=>100),
            'de_num_exterior'            => array('alphaNumber' => true,	    'length'=>5),
            'de_num_interior'            => array('alphaNumber' => true,	    'length'=>5),
            'de_cod_postal'              => array('alphaNumber' => true,	    'length'=>45),
            't1_instrum_notaria'         => array('alphaNumber' => true,	    'length'=>45),
            't1_instrum_estado'          => array('alphaNumber' => true,	    'length'=>5),
            't1_instrum_nom_notario'     => array('alphaNumber' => true,	    'length'=>100),
            't1_instrum_num_instrumento' => array('alphaNumber' => true,	    'length'=>10),
            't2_instrum_notaria'         => array('alphaNumber' => true,	    'length'=>13),
            't2_instrum_estado'          => array('alphaNumber' => true,	    'length'=>100),
            't2_instrum_nom_notario'     => array('alphaNumber' => true,	    'length'=>100),
            't2_instrum_num_instrumento' => array('alphaNumber' => true,	    'length'=>18),
            'pn_instrum_notaria'         => array('alphaNumber' => true, 	    'length'=>18),
            'pn_instrum_estado'          => array('alphaNumber' => true,	    'length'=>255),
            'pn_instrum_nom_notario'     => array('alphaNumber' => true,	    'length'=>100),
            'pn_instrum_num_instrumento' => array('alphaNumber' => true,	    'length'=>10),
            'rp_instrum_num_instrumento' => array('alphaNumber' => true,	    'length'=>10),
            'rp_instrum_estado'          => array('alphaNumber' => true,	    'length'=>10),
            'au_params'                  => array('number' => true,     	    'length'=>2),
            'db_banco_codigo'            => array('alphaNumber' => true,	    'length'=>3),
            'db_banco_cuenta'            => array('alphaNumber' => true,        'length'=>10,   'minlength'=>10),
            'db_banco_sucursal'          => array('alphaNumber' => true,	    'length'=>10),
            'db_banco_clabe'             => array('banco_clabe' => true,	    'length'=>18,   'minlength'=>18),
            'tab'                        => array('string' => true,   	    	'length'=>18),
            'dp_fecha_nacimiento'        => array('date' => true,	        	'length'=>10),
            't1_instrum_fecha'           => array('date' => true,	        	'length'=>10),
            't2_instrum_fecha'           => array('date' => true,	        	'length'=>10),
            'pn_instrum_fecha'           => array('date' => true,	        	'length'=>10),
            'rp_instrum_fecha'           => array('date' => true,	        	'length'=>10));

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


        if($integrado_id === null){
            $integrado_id = getFromTimOne::newintegradoId($data['pj_pers_juridica']);

            $columnas 		= array('user_id', 'integrado_id', 'integrado_principal', 'integrado_permission_level');
            $valores 		= array($data['user_id'], $integrado_id, 1, 3);

            self::insertData('integrado_users', $columnas, $valores);

            $session = JFactory::getSession();
            $session->set('integradoId',$integrado_id,'integrado');

            $data['integradoId'] = $integrado_id;

            JFactory::getSession()->set('integradoId',$integrado_id, 'integrado');
        }

        switch($data['tab']){
            case 'juridica':

                $createdDate  = time();
                $table 		  = 'integrado';
                $columnas 	  = array('integrado_id','status','pers_juridica', 'createdDate');
                $valores	  = array( $integrado_id, '0', $data['pj_pers_juridica'], $createdDate );
                $updateSet 	  = array($db->quoteName('pers_juridica').' = '.$data['pj_pers_juridica'] );
                break;
            case 'personales':
                $table 		= 'integrado_datos_personales';
                $columnas[] = 'integrado_id';
                $valores[]	= $integrado_id;
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
                $columnas[] = 'integrado_id';
                $valores[]	= $integrado_id;

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
            case 'params':
                $table = 'integrado_params';
                $columnas[] = 'integrado_id';
                $valores[]  = $integrado_id;

                foreach ($data as $key => $value) {
                    $columna 	= substr($key, 3);
                    $clave 		= substr($key, 0,3);

                    if($clave == 'au_'){
                        $columnas[] = $columna;
                        $valores[] = $db->quote($value);
                        $updateSet[]	= $db->quoteName($columna).' = '.$db->quote($value);
                        $valoresvalidaicon[$columna] = $value;
                    }
                }
                break;
            case 'bancos':
                $table = 'integrado_datos_bancarios';
                $columnas[] = 'integrado_id';
                $valores[]	= $integrado_id;

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

        $existe = self::checkData($table, $db->quoteName('integrado_id').' = '.$integrado_id);


        if( empty($existe) ){
            $respuesta = self::insertData($table, $columnas, $valores);
        }else{
            $condicion 	= array($db->quoteName('integrado_id').' = '.$integrado_id );
            $respuesta = self::updateData($table, $updateSet, $condicion);
        }

        switch($data['tab']){
            case 'juridica':
                $resultado['nextTab'] = 'basic-details';
                break;
            case 'personales':
                $resultado['nextTab'] = 'empresa';
                break;
            case 'empresa':
                $resultado['nextTab'] = 'params';
                break;
            case 'params':
                $resultado['nextTab'] = 'banco';
                break;
            case 'bancos':
                $resultado['nextTab'] = 'files';
                break;
        }

        $resultado['safeComplete']  = true;
        $resultado['integradoId'] = $integrado_id;

        /*if($data['tab']=='personales'){
            $contenido = JText::_('NOTIFICACIONES_1');
            $contenido = str_replace('$nombre', '<strong style="color: #000000">'.$data['dp_nom_comercial'].'</strong>',$contenido);
            $contenido = str_replace('$folio', '<strong style="color: #000000">$'.$data['user_id'].'</strong>',$contenido);
            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);

            $dato['titulo']         = JText::_('TITULO_1');
            $dato['body']           = $contenido;

            $send                   = new Send_email();
            $send->notification($dato);
        }
*/

        return $resultado;
    }

    public static function checkData($table, $where){
        try{
            $db		= JFactory::getDbo();
            $query 	= $db->getQuery(true);

            $query->select('*')
                ->from($db->quoteName('#__'.$table))
                ->where($where);

            $db->setQuery($query);

            $results = $db->loadObjectList();

            return $results;
        }
        catch(Exception $e){
            $response = array('success' => false , 'msg' => 'Error al guardar intente nuevamente');
            echo json_encode($response);
        }
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
        $columnast1[] 	= 'integrado_id';
        $columnast2[] 	= 'integrado_id';
        $columnasPN[] 	= 'integrado_id';
        $columnasRP[]	= 'integrado_id';
        $columnast1[] 	= 'instrum_type';
        $columnast2[] 	= 'instrum_type';
        $columnasPN[] 	= 'instrum_type';
        $columnasRP[]	= 'instrum_type';

        $valort1[]		= $data['integradoId'];
        $valort2[]		= $data['integradoId'];
        $valorPN[]		= $data['integradoId'];
        $valorRP[]		= $data['integradoId'];
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

        $where = $db->quoteName('integrado_id').' = '.$data['integradoId'].' AND '.$db->quoteName('instrum_type').' = 1';
        $existet1 = self::checkData('integrado_instrumentos', $where);
        if(empty($existet1) ){
            self::insertData('integrado_instrumentos', $columnast1, $valort1);
            $existet1 = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSett1, $where);
            $existet1 = self::checkData('integrado_instrumentos', $where);
        }
        self::saveInstrumentosEmpresa($data['integradoId'], $existet1[0]->id, 'testimonio_1');

        $where = $db->quoteName('integrado_id').' = '.$data['integradoId'].' AND '.$db->quoteName('instrum_type').' = 2';
        $existet2 = self::checkData('integrado_instrumentos', $where);
        if(empty($existet2) ){
            self::insertData('integrado_instrumentos', $columnast2, $valort2);
            $existet2 = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSett2, $where);
            $existet2 = self::checkData('integrado_instrumentos', $where);
        }
        self::saveInstrumentosEmpresa($data['integradoId'], $existet2[0]->id, 'testimonio_2');

        $where = $db->quoteName('integrado_id').' = '.$data['integradoId'].' AND '.$db->quoteName('instrum_type').' = 3';
        $existepn = self::checkData('integrado_instrumentos', $where);
        if(empty($existepn) ){
            self::insertData('integrado_instrumentos', $columnasPN, $valorPN);
            $existepn = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSetpn, $where);
            $existepn = self::checkData('integrado_instrumentos', $where);
        }
        self::saveInstrumentosEmpresa($data['integradoId'], $existepn[0]->id, 'poder');

        $where = $db->quoteName('integrado_id').' = '.$data['integradoId'].' AND '.$db->quoteName('instrum_type').' = 4';
        $existerp = self::checkData('integrado_instrumentos', $where);
        if(empty($existerp) ){
            self::insertData('integrado_instrumentos', $columnasRP, $valorRP);
            $existerp = self::checkData('integrado_instrumentos', $where);
        }else{
            self::updateData('integrado_instrumentos', $updateSetrp, $where);
            $existerp = self::checkData('integrado_instrumentos', $where);
        }
        self::saveInstrumentosEmpresa($data['integradoId'], $existerp[0]->id, 'reg_propiedad');
    }

    public static function saveInstrumentosEmpresa($integrado_id, $id_instrumento, $campo){
        $db				= JFactory::getDbo();
        $where			= $db->quoteName('integrado_id').' = '.$integrado_id;
        $dataEmpresa 	= self::checkData('integrado_datos_empresa', $where);
        $columna[] 		= $campo;
        $columna[]		= 'integrado_id';
        $valor[]		= $id_instrumento;
        $valor[]		= $integrado_id;
        $updateSet[] 	= $db->quoteName($campo).' = '.$db->quote($id_instrumento);

        if(empty($dataEmpresa)){
            self::insertData('integrado_datos_empresa', $columna, $valor);
        }else{
            self::updateData('integrado_datos_empresa', $updateSet, $where);
        }
    }

    public function createNewSolicitud() {
        $this->sesion->clear('integradoId', 'integrado');
        JFactory::getApplication()->redirect('index.php?option=com_integrado&view=solicitud');
    }

    public function select() {
        $this->app = JFactory::getApplication();

        if (!JSession::checkToken()) {
            $this->redirectToSelectIntegrado();
        } else {
            $this->id = $this->app->input->get('integradoId', null, 'INT');

            list($valid, $integrado) = $this->isValidIntegradoIdForUser();
            if ($valid) {

                $sesion = JFactory::getSession();
                $sesion->set('integradoId', $this->id, 'integrado');

                $this->app->redirect( 'index.php?option=com_mandatos', JText::sprintf( 'LBL_CHANGED_TO_INTEGRADO' , $integrado->displayName) );
            }

            $this->redirectToSelectIntegrado();
        }


    }

    public function isValidIntegradoIdForUser() {
        $model = $this->getModel();
        $integrados = $model->getIntegrados();
        $check = array_key_exists($this->id, $integrados);

        return array($check, $integrados[$this->id]);
    }

    public function redirectToSelectIntegrado() {
        $this->app->redirect( 'index.php?option=com_integrado&view=integrado&layout=change', JText::_( 'LBL_ERROR' ), 'error' );
    }
}