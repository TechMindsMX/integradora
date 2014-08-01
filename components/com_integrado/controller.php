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
		
		$response['respuesta'] = self::insertData(JRequest::get());
		// Get the document object.
		$document = JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
		
		echo json_encode($response);
	}
	
	public static function insertData($data){
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*')
				->from($db->quoteName('#__integrado'))
				->where($db->quoteName('integrado_id') . ' = '. $db->quote($data['integrado_Id']));
 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}	
}
