<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controllerform');

/**
 * 
 */
class IntegradoControllerIntegrado extends JControllerForm {
	
	public $id;
	
	function __construct(){
		var_dump($this);
		$this->id = JRequest::get('id');
	}

}

