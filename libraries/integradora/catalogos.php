<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.factory');


/**
 * Clase catalogos
 */
class Catalogos {
	
	public function getNacionalidades()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__paises_catalog'));
		$result->nacionalidad = $db->setQuery($query)->loadObjectList();
		
		return $result;
	}
}
	