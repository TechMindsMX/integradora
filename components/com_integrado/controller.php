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
	function savealta(){
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$data = $input->getArray();

		$columnas	= array('integrado_id','user_id', 'integrado_principal', 'integrado_permission_level');
		$update		= array( $db->quoteName('integrado_permission_level').'= '.$db->quote($data['permission_level']));
		$valores	= array($data['integrado_id'], $data['userId'], 0, $data['permission_level']);

		$existe = self::checkData('integrado_users', $db->quoteName('user_id').' = '.$data['userId'].' AND '.$db->quoteName('integrado_id').' = '.$data['integrado_id']);

		if( is_null($existe) ){
			self::insertData('integrado_users', $columnas, $valores);
		}else{
			self::updateData('integrado_users', $update, $db->quoteName('user_id').' = '.$data['userId']);
		}
		
		JApplication::redirect('index.php?option=com_integrado&view=altausuarios', false);
	}
	
	function uploadFiles(){
		$db = JFactory::getDbo();
		$usuario = JFactory::getUser()->id;
		$usuario = self::checkData('integrado_users', $db->quoteName('user_id').' = '.$usuario);

		foreach ($_FILES as $key => $value) {
			manejoImagenes::cargar_imagen($value['type'], $usuario['integrado_id'], $value, $key);
			$columna 	= substr($key, 3);
			$clave 		= substr($key, 0,3);
			$where		= $db->quoteName('integrado_id').' = '.$usuario['integrado_id'];
			
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
					$where = $db->quoteName('integrado_id').' = '.$usuario['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 1';
					break;
				case 't2_':
					$table = 'integrado_instrumentos';
					$where = $db->quoteName('integrado_id').' = '.$usuario['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 2';
					break;
				case 'pn_':
					$table = 'integrado_instrumentos';
					$where = $db->quoteName('integrado_id').' = '.$usuario['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 3';
					break;
				case 'rp_':
					$table = 'integrado_instrumentos';
					$where = $db->quoteName('integrado_id').' = '.$usuario['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 4';
					break;
				
				default:
					
					break;
			}
			$updateSet 	= array($db->quoteName($columna).' = '.$db->quote("media/archivosJoomla/" . $usuario['integrado_id'].'_'.$key . ".jpg") );
			self::updateData($table, $updateSet, $where);
		}
		JApplication::redirect('index.php?option=com_integrado&view=solicitud', false);
	}
	
	function saveform(){
		if (JSession::checkToken() === false) {
			$response = array('success' => false );
			echo json_encode($response);
			return true;
		}
		$input = JFactory::getApplication()->input;
		$post = $input->getArray();
		
		$response = self::manejoDatos($post);
		// Get the document object.
		$document = JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		echo json_encode($response);
	}
	
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
	
	function checkUser(){
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$email = $input->getArray();
		
		$respuesta = self::checkData('users', $db->quoteName('email').' = '.$db->quote($email['data']));
		
		if(!is_null($respuesta)){
			$response = array('success' => true, 'name' => $respuesta['name'], 'userId' => $respuesta['id'], 'delete' => false);
		}else{
			$response = array('success' => false, 'msg' => 'El usuario no existe');
		}
		$document = JFactory::getDocument();
		
		$document->setMimeEncoding('application/json');
		
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
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
		
		$integrado = self::checkdata('integrado_users', $db->quoteName('user_id').' = '.$data['user_id'].' AND '.$db->quoteName('integrado_principal').' = 1');
		
		if( is_null($integrado) ){
			$integrado_id = getFromTimOne::getIntegradoId(); //self::checkData('integrado_users', $db->quoteName('user_id').' = '.$data['user_id']);
			
			$integrado_id = $integrado_id['integrado_id']+1;
			
			$columnas = array('user_id', 'integrado_id', 'integrado_principal', 'integrado_permission_level');
			$valores = array($data['user_id'], $integrado_id, 1, 3);
			
			self::insertData('integrado_users', $columnas, $valores);

		}else{
			$integrado_id = $integrado['integrado_id'];
		}

		$data['integrado_id'] = $integrado_id;

		switch($data['tab']){
			case 'juridica':
				$table 		  = 'integrado';
				$columnas 	  = array('integrado_id','status','pers_juridica');
				$valores	  = array( $integrado_id, '0', $data['pers_juridica'] );
				$updateSet 	  = array($db->quoteName('pers_juridica').' = '.$data['pers_juridica'] );
				
				$diccionario  = array('integrado_id' 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_INTEGRADO_ID'),		'length'=>10),
									  'status'		 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_STATUS'),			'length'=>10),
									  'pers_juridica'	=> array('tipo'=>'number',		'label'=>JText::_('LBL_PERSONALIDADJ'),		'length'=>10));
									  
				validador::procesamiento($data, $diccionario, $data['tab']);
				
				break;
			case 'personales':
				$table 		= 'integrado_datos_personales';
				$columnas[] = 'integrado_id';
				$valores[]	= $integrado_id;
				$valoresvalidaicon['integrado_id']= $integrado_id;
				
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'number',													'length'=>10),
									  'nacionalidad'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_NACIONALIDAD'),		'length'=>45),
									  'sexo'			 	=> array('tipo'=>'string',		'label'=>JText::_('LBL_SEXO'),				'length'=>45),
									  'calle'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CALLE'),				'length'=>45),
									  'rfc'					=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),				'length'=>45),
									  'num_exterior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_EXT'),				'length'=>45),
									  'num_interior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_INT'),				'length'=>45),
									  'cod_postal'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_CP'),				'length'=>5,	'minlength' => 5),
									  'tel_fijo'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_TEL_FIJO'),			'length'=>10,	'minlength' => 10),
									  'tel_fijo_extension'	=> array('tipo'=>'number',		'label'=>JText::_('LBL_EXT'),				'length'=>10),
									  'tel_movil' 			=> array('tipo'=>'number',		'label'=>JText::_('LBL_TEL_MOVIL'),			'length'=>13,	'minlength' => 13),
									  'email' 				=> array(						'label'=>JText::_('LBL_CORREO'),			'length'=>100),
									  'nom_comercial'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_NOM_COMERCIAL'),		'length'=>100),
									  'curp' 				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CURP'),				'length'=>18,	'minlength' => 18),
									  'fecha_nacimiento'	=> array(						'label'=>JText::_('LBL_FECHA_NACIMIENTO'),	'length'=>10));
									  
				validador::procesamiento($data, $diccionario,$data['tab']);
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int',													'length'=>10),
									  'razon_social'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RAZON_SOCIAL'),	'length'=>255),
									  'rfc'				 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),			'length'=>45),
									  'calle'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CALLE'),			'length'=>45),
									  'num_exterior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_EXT'),			'length'=>45),
									  'num_interior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_INT'),			'length'=>45),
									  'cod_postal'			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CP'),			'length'=>5,	'minlength' => 5),
									  'tel_fijo'			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_TEL_FIJO'),		'length'=>10,	'minlength' => 10),
									  'tel_fijo_extension'	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_EXT'),			'length'=>10),
									  'sitio_web' 			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CORREO'),		'length'=>255),
									  'tel_fax'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),			'length'=>10),
									  'testimonio_1'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),			'length'=>10),
									  'testimonio_2' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),			'length'=>10),
									  'poder'				=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),			'length'=>10),
									  'reg_propiedad' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),			'length'=>10));
				
				validador::procesamiento($data, $diccionario,$data['tab']);
				
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'number',												'length'=>10),
									  'banco_nombre'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCOS'),		'length'=>5),
									  'banco_cuenta'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCO_CUENTA'),	'length'=>10,	'minlength' => 10),
									  'banco_sucursal'		=> array('tipo'=>'number',		'label'=>JText::_('LBL_BANCO_SUCURSAL'),'length'=>3),
									  'banco_clabe'			=> array('tipo'=>'number',		'label'=>JText::_('LBL_NUMERO_CLABE'),	'length'=>18),	'minlength' => 18);
				
				validador::procesamiento($data, $diccionario,$data['tab']);
				
				break;
		}

		$existe = self::checkData($table, $db->quoteName('integrado_id').' = '.$integrado_id);
		
		if( is_null($existe) ){
			 $respuesta = self::insertData($table, $columnas, $valores);
		}else{
			$condicion 	= array($db->quoteName('integrado_id').' = '.$integrado_id ); 
			$respuesta = self::updateData($table, $updateSet, $condicion);
		}
		
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
		 
			$results = $db->loadAssoc();
	
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
		
		$valort1[]		= $data['integrado_id'];
		$valort2[]		= $data['integrado_id'];
		$valorPN[]		= $data['integrado_id'];
		$valorRP[]		= $data['integrado_id'];
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

		$where = $db->quoteName('integrado_id').' = '.$data['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 1';		
		$existet1 = self::checkData('integrado_instrumentos', $where);
		if(is_null($existet1) ){
			self::insertData('integrado_instrumentos', $columnast1, $valort1);
			$existet1 = self::checkData('integrado_instrumentos', $where);
		}else{
			self::updateData('integrado_instrumentos', $updateSett1, $where);
			$existet1 = self::checkData('integrado_instrumentos', $where);
		}
		self::saveInstrumentosEmpresa($data['integrado_id'], $existet1['id'], 'testimonio_1');

		$where = $db->quoteName('integrado_id').' = '.$data['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 2';		
		$existet2 = self::checkData('integrado_instrumentos', $where);
		if(is_null($existet2) ){
			self::insertData('integrado_instrumentos', $columnast2, $valort2);
			$existet2 = self::checkData('integrado_instrumentos', $where);
		}else{
			self::updateData('integrado_instrumentos', $updateSett2, $where);
			$existet2 = self::checkData('integrado_instrumentos', $where);
		}
		self::saveInstrumentosEmpresa($data['integrado_id'], $existet2['id'], 'testimonio_2');
		
		$where = $db->quoteName('integrado_id').' = '.$data['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 3';		
		$existepn = self::checkData('integrado_instrumentos', $where);
		if(is_null($existepn) ){
			self::insertData('integrado_instrumentos', $columnasPN, $valorPN);
			$existepn = self::checkData('integrado_instrumentos', $where);
		}else{
			self::updateData('integrado_instrumentos', $updateSetpn, $where);
			$existepn = self::checkData('integrado_instrumentos', $where);
		}
		self::saveInstrumentosEmpresa($data['integrado_id'], $existepn['id'], 'poder');

		$where = $db->quoteName('integrado_id').' = '.$data['integrado_id'].' AND '.$db->quoteName('instrum_type').' = 4';		
		$existerp = self::checkData('integrado_instrumentos', $where);
		if(is_null($existerp) ){
			self::insertData('integrado_instrumentos', $columnasRP, $valorRP);
			$existerp = self::checkData('integrado_instrumentos', $where);
		}else{
			self::updateData('integrado_instrumentos', $updateSetrp, $where);
			$existerp = self::checkData('integrado_instrumentos', $where);
		}
		self::saveInstrumentosEmpresa($data['integrado_id'], $existerp['id'], 'reg_propiedad');
	}

	public static function saveInstrumentosEmpresa($integrado_id, $id_instrumento, $campo){
		$db				= JFactory::getDbo();
		$where			= $db->quoteName('integrado_id').' = '.$integrado_id;
		$dataEmpresa 	= self::checkData('integrado_datos_empresa', $where);
		$columna[] 		= $campo;
		$valor[]		= $id_instrumento;
		$updateSet[] 	= $db->quoteName($campo).' = '.$db->quote($id_instrumento);
		
		if(is_null($dataEmpresa)){
			self::insertData('integrado_datos_empresa', $columna, $valor);
		}else{
			self::updateData('integrado_datos_empresa', $updateSet, $where);
		}
	}
}