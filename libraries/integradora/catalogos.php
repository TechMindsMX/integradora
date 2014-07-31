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
			->from($db->quoteName('#__catalog_paises'))
			->order('nombre ASC');
		$result = $db->setQuery($query)->loadObjectList();
		
		$this->nacionalidades = $result;
	}
	public function getEstados()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__catalog_estados'))
			->order('nombre ASC');
		$result = $db->setQuery($query)->loadObjectList();
		
		$this->estados = $result;
	}
}
	