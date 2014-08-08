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
	
	public function getBancos(){
		$catalogo = json_decode(@file_get_contents(MIDDLE.PUERTO.TIMONE.'stp/listBankCodes'));
		
		foreach ($catalogo as $key => $value) {
			$objeto = new stdClass;
			
			$objeto->banco = $value->name;
			$objeto->clave = $value->bankCode;
			$objeto->claveClabe = substr($value->bankCode, -3);
			
			$cat[] = $objeto;
		}

		$this->bancos = $cat;
	}
	
	public function getStatusSolicitud()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__integrado_status_catalog'));
		$status = $db->setQuery($query)->loadObjectList(); 

		$this->statusSolicitud = $status;
	}
}
	