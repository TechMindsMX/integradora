<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();

if($currUser->guest){
	$app->redirect('index.php/login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
}

class IntegradoController extends JControllerLegacy {
	//Revisa si el usaurio existe dado un correo electronico
	function checkUser(){
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$email = $input->getArray();
		
		$respuesta = self::checkData('users', $db->quoteName('email').' = '.$db->quote($email['data']));
		
		if(!is_null($respuesta)){
			$response = array('success' => true, 'name' => $respuesta[0]->name, 'userId' => $respuesta[0]->id, 'delete' => false);
		}else{
			$response = array('success' => false, 'msg' => 'El usuario no existe');
		}
		$document = JFactory::getDocument();
		
		$document->setMimeEncoding('application/json');
		
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
		echo json_encode($response);
	}
		
	//Salva la alta de usuarios a un integrado
	function savaAltaNewUserOfInteg(){
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$data = $input->getArray();

		$columnas	= array('integrado_id','user_id', 'integrado_principal', 'integrado_permission_level');
		$update		= array( $db->quoteName('integrado_permission_level').'= '.$db->quote($data['permission_level']));
		$valores	= array($data['integrado_id'], $data['userId'], 0, $data['permission_level']);

		$existe = self::checkData('integrado_users', $db->quoteName('user_id').' = '.$data['userId'].' AND '.$db->quoteName('integrado_id').' = '.$data['integrado_id']);
		
		if( empty($existe) ){
			self::insertData('integrado_users', $columnas, $valores);
		}else{
			self::updateData('integrado_users', $update, $db->quoteName('user_id').' = '.$data['userId']);
		}
		
		JApplication::redirect('index.php?option=com_integrado&view=altausuarios&integradoId='.$data['integrado_id'], false);
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
		$db 	= JFactory::getDbo();
		$data	= JFactory::getApplication()->input->getArray();
		$integrado_id = $data['integradoId']!=''?$data['integradoId']:'';
		
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
			self::updateData($table, $updateSet, $where);
		}

		if($integrado_id==''){
			$url = 'index.php?option=com_integrado&view=solicitud';
		}else{
			$url = 'index.php?option=com_integrado&view=solicitud&integradoId='.$integrado_id;
		}
		
		JApplication::redirect($url, false);
	}
	
	//Recibe el post y lo envia a procesar y guardar
	function saveform(){
		if (JSession::checkToken() === false) {
			$response = array('success' => false, 'msg'=>'Token Invalido' );
			echo json_encode($response);
			return true;
		}
		$input 	= JFactory::getApplication()->input;
		$post 	= $input->getArray();
		
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
		$cp = $input->getArray();

		$url = "http://192.168.0.122:7272/sepomex-middleware/rest/sepomex/get/".$cp['cp'];

		echo file_get_contents($url);
	}
	
	public static function manejoDatos($data){
		$db	= JFactory::getDbo();
		$integrado_id = empty($data['integradoId']) ? true : $data['integradoId'];

		$user = JFactory::getUser();
		if($integrado_id === true){
			$callback		= JRoute::_('index.php?option=com_integrado&view=solicitud');
			$integrado_id 	= getFromTimOne::newIntegradoId(array('email' => $user->email,'name' => $user->name), $callback);
			$columnas 		= array('user_id', 'integrado_id', 'integrado_principal', 'integrado_permission_level');
			$valores 		= array($data['user_id'], $integrado_id, 1, 3);
			
			self::insertData('integrado_users', $columnas, $valores);
			
			$data['integradoId'] = $integrado_id;
		}

		$diccionario  = array('integradoId' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_INTEGRADO_ID'),		'length'=>10),
							  'status'		 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_STATUS'),			'length'=>10),
							  'pers_juridica'		=> array('tipo'=>'number',		'label'=>JText::_('LBL_PERSONALIDADJ'),		'length'=>10),
							  'nacionalidad'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_NACIONALIDAD'),		'length'=>45),
							  'sexo'			 	=> array('tipo'=>'string',		'label'=>JText::_('LBL_SEXO'),				'length'=>45),
							  'calle'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CALLE'),				'length'=>45),
							  'rfc'					=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),				'length'=>45),
							  'num_exterior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_EXT'),				'length'=>45),
							  'num_interior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_INT'),				'length'=>45),
							  'cod_postal'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_CP'),				'length'=>5),
							  'tel_fijo'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_TEL_FIJO'),			'length'=>10),
							  'tel_fijo_extension'	=> array('tipo'=>'number',		'label'=>JText::_('LBL_EXT'),				'length'=>10),
							  'tel_movil' 			=> array('tipo'=>'number',		'label'=>JText::_('LBL_TEL_MOVIL'),			'length'=>13),
							  'email' 				=> array(						'label'=>JText::_('LBL_CORREO'),			'length'=>100),
							  'nom_comercial'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_NOM_COMERCIAL'),		'length'=>100),
							  'curp' 				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CURP'),				'length'=>18),
							  'fecha_nacimiento'	=> array(						'label'=>JText::_('LBL_FECHA_NACIMIENTO'),	'length'=>10),
							  'razon_social'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RAZON_SOCIAL'),		'length'=>255),
							  'tel_fax'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),				'length'=>10),
							  'testimonio_1'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),				'length'=>10),
							  'testimonio_2' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),				'length'=>10),
							  'poder'				=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),				'length'=>10),
							  'reg_propiedad' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),				'length'=>10),
							  'banco_nombre'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCOS'),			'length'=>5),
							  'banco_cuenta'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCO_CUENTA'),		'length'=>10),
							  'banco_sucursal'		=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCO_SUCURSAL'),	'length'=>3),
							  'banco_clabe'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_NUMERO_CLABE'),		'length'=>18));
							  
		//envia la data a validacion y regresa un arreglo con los resultados para cada uno de los campos que esten llenados
		$resultado = validador::procesamiento($data, $diccionario);

		foreach ($resultado as $key => $value) {
			if( is_array($value) ){
				return $value;
			}
		}

		switch($data['tab']){
			case 'juridica':
				$table 		  = 'integrado';
				$columnas 	  = array('integrado_id','status','pers_juridica');
				$valores	  = array( $integrado_id, '0', $data['pj_pers_juridica'] );
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
		
		$respuesta['integradoId'] = $integrado_id;
		
		return $respuesta;
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
			var_dump($e);
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
			var_dump($e);
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
}