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
		// Get the document object.
		$document = JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
		
		echo json_encode($response);
	}	
}
