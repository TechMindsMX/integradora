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
				$diccionario  = array('integrado_id' 	=> array('tipo'=>'int','length'=>10),
									  'status'		 	=> array('tipo'=>'int','length'=>10),
									  'pers_juridica'	=> array('tipo'=>'int','length'=>10));
									  
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
				$diccionario  = array('integrado_id'		=> array('tipo'=>'number',		'length'=>10),
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
									  'email' 				=> array('label'=>JText::_('LBL_CORREO'),			'length'=>100),
									  'nom_comercial'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_NOM_COMERCIAL'),		'length'=>100),
									  'curp' 				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CURP'),				'length'=>18,	'minlength' => 18),
									  'fecha_nacimiento'	=> array('label'=>JText::_('LBL_FECHA_NACIMIENTO'),	'length'=>10));
									  
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int',		'length'=>10),
									  'razon_social'	 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RAZON_SOCIAL'),	'length'=>255),
									  'rfc'				 	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),		'length'=>45),
									  'calle'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CALLE'),		'length'=>45),
									  'num_exterior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_EXT'),		'length'=>45),
									  'num_interior'		=> array('tipo'=>'alphaNumber',	'label'=>JText::_('NUM_INT'),		'length'=>45),
									  'cod_postal'			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CP'),		'length'=>5,	'minlength' => 5),
									  'tel_fijo'			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_TEL_FIJO'),	'length'=>10,	'minlength' => 10),
									  'tel_fijo_extension'	=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_EXT'),		'length'=>10),
									  'sitio_web' 			=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_CORREO'),	'length'=>255),
									  'tel_fax'				=> array('tipo'=>'alphaNumber',	'label'=>JText::_('LBL_RFC'),		'length'=>10),
									  'testimonio_1'	 	=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),		'length'=>10),
									  'testimonio_2' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),		'length'=>10),
									  'poder'				=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),		'length'=>10),
									  'reg_propiedad' 		=> array('tipo'=>'number',		'label'=>JText::_('LBL_RFC'),		'length'=>10));
				
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
				
				$diccionario  = array('integrado_id'		=> array('tipo'=>'int',		'length'=>10),
									  'banco_nombre'	 	=> array('tipo'=>'string',	'label'=>JText::_('LBL_BANCOS'),		'length'=>5),
									  'banco_cuenta'	 	=> array('tipo'=>'int',		'label'=>JText::_('LBL_BANCO_CUENTA'),	'length'=>18,	'minlength' => 18),
									  'banco_sucursal'		=> array('tipo'=>'int',		'label'=>JText::_('LBL_BANCO_SUCURSAL'),'length'=>10),
									  'banco_clabe'			=> array('tipo'=>'int',		'label'=>JText::_('LBL_NUMERO_CLABE'),	'length'=>18),	'minlength' => 18);
				
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
			exit;
		}
	}
	
	function sepomex(){
		$url = "http://192.168.0.122:7272/sepomex-middleware/rest/sepomex/get/".$_POST["cp"];
		echo file_get_contents($url);
	}
}
