<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');


/**
 * Clase para consulta a la base de datos
 */
class querysDB{
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
}

?>