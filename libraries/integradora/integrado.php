<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');


/**
 * Clase datos de integrado
 */
class Integrado {
	
	protected $user;
	
	
	function __construct() {
		$this->user = JFactory::getUser();
		
		// Se crea variable interna para acceso a base de datos
		$db = JFactory::getDbo();

		$this->integrados = $this->getIntegradosCurrUser($db);
		
		$this->nombres = $this->separaNombre($this->user->name);
		
		unset($this->user->password);
	}
	function getIntegradosCurrUser($db)
	{
		$query = $db->getQuery(true)
			->select($db->quoteName('integrado_id'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('user_id') . '=' . $db->quote($this->user->id));
		$result = $db->setQuery($query)->loadObjectList();
		
		$instance->intergrado->ids = $result;
		
		return $result;
	}
	function separaNombre($value)
	{
		
	}
}

