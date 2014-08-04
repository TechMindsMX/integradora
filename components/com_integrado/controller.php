<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class IntegradoController extends JControllerLegacy {
	function saveform(){
		if (JSession::checkToken() === false) {
			$response = array('success' => false );
			echo json_encode($response);
			return true;
		}

		$response = array('success' => true );
		
		$response['respuesta'] = self::manejoDatos(JRequest::get());
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
		
		switch($data['tab']){
			case 'juridica':
				$table 		= 'integrado';
				$columnas 	= array('integrado_id','status','pers_juridica');
				$valores	= array( $integrado_id, '0', $data['pers_juridica'] );
				break;
			case 'personales':
				$table 		= 'integrado_datos_personales';
				$columnas[] = 'integrado_id';
				$valores[]	= $integrado_id;
				
				foreach ($data as $key => $value) {
					$columna 	= substr($key, 3);
					$clave 		= substr($key, 0,2);
					
					if($clave == 'dp'){
						$columnas[] = $columna;
						$valores[] = $db->quote($value);
					}
				}
				break;
			case 'empresa':
				$table = 'integrado_datos_empresa';
				$columnas[] = 'integrado_id';
				$valores[]	= $integrado_id;
				
				foreach ($data as $key => $value) {
					$columna 	= substr($key, 3);
					$clave 		= substr($key, 0,2);
					
					if($clave == 'de'){
						$columnas[] = $columna;
						$valores[] = $db->quote($value);
					}
				}
				break;
			case 'bancos':
				$table = 'integrado_datos_bancarios';
				$columnas[] = 'integrado_id';
				$valores[]	= $integrado_id;
				
				foreach ($data as $key => $value) {
					$columna 	= substr($key, 3);
					$clave 		= substr($key, 0,2);
					
					if($clave == 'db'){
						$columnas[] = $columna;
						$valores[] = $db->quote($value);
					}
				}
				break;
		}

		$existe = self::checkData($integrado_id, $table, 'integrado_id');

		if( is_null($existe) ){
			$algo = self::insertData($table, $columnas, $valores);
		}
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
			
			return 'saved';
			
		}
		catch(Exception $e){
			$response = array('success' => false , 'msg' => 'Error al guardar intente nuevamente');
			echo json_encode($response);
		}
	}
	
		
}
