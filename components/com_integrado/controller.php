<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');

class IntegradoController extends JControllerLegacy {
	function saveform(){
		if (JSession::checkToken() === false) {
			$response = array('success' => false );
			echo json_encode($response);
			return true;
		}
		
		$response = self::manejoDatos(JRequest::get());
		// Get the document object.
		$document = JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
		
		echo json_encode($response);
	}
	
	public static function manejoDatos($data){
		$db	= JFactory::getDbo();
		
		$integrado = self::checkdata($data['user_id'], 'integrado_users', 'user_id');
		
		if( is_null($integrado) ){
			$columnas = array('user_id');
			$valores = array($data['user_id']);
			
			self::insertData('integrado_users', $columnas, $valores);
			
			$integrado_id = self::checkData($data['user_id'], 'integrado_users', 'user_id');
			
			$integrado_id = $integrado_id['integrado_id'];
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
				$diccionario  = array('integrado_id' 	=> 'int',
									  'status'		 	=> 'int',
									  'pers_juridica'	=> 'int');
									  
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
						$columnas[] 	= $columna;
						$valores[] 		= $db->quote($value);
						$updateSet[]	= $db->quoteName($columna).' = '.$db->quote($value);
						$valoresvalidaicon[$columna] = $value;
					}
				}
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int','length'=>10),
									  'nacionalidad'	 	=> array('tipo'=>'string','length'=>45),
									  'sexo'			 	=> array('tipo'=>'string','length'=>45),
									  'calle'				=> array('tipo'=>'string','length'=>45),
									  'rfc'					=> array('tipo'=>'string','length'=>45),
									  'num_exterior'		=> array('tipo'=>'string','length'=>45),
									  'num_interior'		=> array('tipo'=>'string','length'=>45),
									  'cod_postal'			=> array('tipo'=>'string','length'=>5),
									  'tel_fijo'			=> array('tipo'=>'string','length'=>10),
									  'tel_fijo_extension'	=> array('tipo'=>'string','length'=>10),
									  'tel_movil' 			=> array('tipo'=>'string','length'=>13),
									  'email' 				=> array('tipo'=>'string','length'=>100),
									  'nom_comercial'	 	=> array('tipo'=>'string','length'=>100),
									  'curp' 				=> array('tipo'=>'string','length'=>18),
									  'fecha_nacimiento'	=> array('tipo'=>'string','length'=>10));
									  
				validador::procesamiento($data, $diccionario,$data['tab']);
				break;
			case 'empresa':
				$table = 'integrado_datos_empresa';
				$columnas[] = 'integrado_id';
				$valores[]	= $integrado_id;
				
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int','length'=>10),
									  'razon_social'	 	=> array('tipo'=>'string','length'=>255),
									  'rfc'				 	=> array('tipo'=>'string','length'=>45),
									  'calle'				=> array('tipo'=>'string','length'=>45),
									  'num_exterior'		=> array('tipo'=>'string','length'=>45),
									  'num_interior'		=> array('tipo'=>'string','length'=>45),
									  'cod_postal'			=> array('tipo'=>'string','length'=>5),
									  'tel_fijo'			=> array('tipo'=>'string','length'=>10),
									  'tel_fijo_extension'	=> array('tipo'=>'string','length'=>10),
									  'sitio_web' 			=> array('tipo'=>'string','length'=>255),
									  'tel_fax'				=> array('tipo'=>'string','length'=>100),
									  'testimonio_1'	 	=> array('tipo'=>'int','length'=>10),
									  'testimonio_2' 		=> array('tipo'=>'int','length'=>10),
									  'poder'				=> array('tipo'=>'int','length'=>10),
									  'reg_propiedad' 		=> array('tipo'=>'int','length'=>10));
				
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int','length'=>10),
									  'banco_nombre'	 	=> array('tipo'=>'string','length'=>255),
									  'banco_cuenta'	 	=> array('tipo'=>'string','length'=>45),
									  'banco_sucursal'		=> array('tipo'=>'string','length'=>45),
									  'banco_clabe'			=> array('tipo'=>'string','length'=>45));
				
				validador::procesamiento($data, $diccionario,$data['tab']);
				
				break;
		}

		$existe = self::checkData($integrado_id, $table, 'integrado_id');
		if( is_null($existe) ){
			 $respuesta = self::insertData($table, $columnas, $valores);
		}else{
			$condicion 	= array($db->quoteName('integrado_id').' = '.$integrado_id ); 
			$respuesta = self::updateData($table, $updateSet, $condicion);
		}
		
		return $respuesta;
	}
	
	public static function checkData($userId, $table, $where){
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('*')
		      ->from($db->quoteName('#__'.$table))
			  ->where($db->quoteName($where).' = '.$userId);

		$db->setQuery($query);
	 
		$results = $db->loadAssoc();

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
			
			return array('success' => false , 'msg' => 'Datos Almacenados correctamente');
			
		}
		catch(Exception $e){
			echo $db->getErrorMsg();
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
			
			return array('success' => false , 'msg' => 'Datos Actualizados correctamente');
			
		}
		catch(Exception $e){
			echo $db->getErrorMsg();
			$response = array('success' => false , 'msg' => 'Error al Actualizar intente nuevamente');
			echo json_encode($response);
		}
	}
	
	function sepomex(){
		$url = "http://192.168.0.122:7272/sepomex-middleware/rest/sepomex/get/".$_POST["cp"];
		echo file_get_contents($url);
	}
}
