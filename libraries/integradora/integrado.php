<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');


/**
 * Clase datos de integrado
 */
class Integrado {
	
	protected $user;
	
	protected $db;
	
	function __construct() {
		$this->user = JFactory::getUser();
		$this->db = JFactory::getDbo();

		$this->integrados = $this->getIntegradosCurrUser();
		
		$this->nombres = $this->separaNombre($this->user->name);
	}
	function getIntegradosCurrUser()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('integrado_id'))
			->from($this->db->quoteName('#__integrado_users'))
			->where($this->db->quoteName('user_id') . '=' . $this->db->quote($this->user->id));
		$result = $this->db->setQuery($query)->loadObjectList();
		
		$instance->intergrado->ids = $result;
		
		return $result;
	}
	function separaNombre($value)
	{
		
	}
}

