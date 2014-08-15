<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');


/**
 * Clase datos de integrado
 */
class Integrado {
	
	public $user;
	
	
	function __construct($integ_id = null) {
		$this->user = JFactory::getUser();
		
		$this->integrados = $this->getIntegradosCurrUser();

		foreach ($this->integrados as $key => $value) {
			$id = $value->integrado_id;
			$this->getSolicitud($id, $key);
		}
		
		$this->nombres = $this->separaNombre($this->user->name);
		
		unset($this->user->password);
	}
	function getIntegradosCurrUser()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select($db->quoteName('integrado_id').','.$db->quoteName('integrado_principal'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('user_id') . '=' . $db->quote($this->user->id));
		$result = $db->setQuery($query)->loadObjectList();
		
		$instance->intergrado->ids = $result;
		
		return $result;
	}
	public function getUsersOfIntegrado($integ_id)
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select($db->quoteName('user_id'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('integrado_id') . '=' . $integ_id);
		$result = $db->setQuery($query)->loadObjectList();
		
		foreach ($result as $key => $value) {
			$result[$key] = JFactory::getUser($value->user_id);
			unset($result[$key]->password);
		}
		
		return $result;
	}
	function separaNombre($value)
	{
	}
	
	function getSolicitud($integ_id = null, $key){
		if ($integ_id == null){
			$this->integrados[$key]->gral 				= self::selectDataSolicitud('integrado_users', 'user_id', $this->user->id);
		}
		$integrado_id 					= isset($this->gral->integrado_id) ? $this->gral->integrado_id : $integ_id;
		
		if(!is_null($integrado_id)){
			$this->integrados[$key]->integrado 			= self::selectDataSolicitud('integrado', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_personales 	= self::selectDataSolicitud('integrado_datos_personales', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_empresa 		= self::selectDataSolicitud('integrado_datos_empresa', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_bancarios	= self::selectDataSolicitud('integrado_datos_bancarios', 'integrado_id', $integrado_id);
			
			$empresa = $this->integrados[$key]->datos_empresa;
			$this->integrados[$key]->testimonio1		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_1);
			$this->integrados[$key]->testimonio2		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_2);
			$this->integrados[$key]->poder				= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->poder);
			$this->integrados[$key]->reg_propiedad		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->reg_propiedad);
		}else{
			$this->integrados[$key]->integrado 			= null;
			$this->integrados[$key]->datos_personales 	= null;
			$this->integrados[$key]->datos_empresa 		= null;
			$this->integrados[$key]->datos_bancarios 	= null;
			
			$this->integrados[$key]->testimonio1		= null;
			$this->integrados[$key]->testimonio2		= null;
			$this->integrados[$key]->poder				= null;
			$this->integrados[$key]->reg_propiedad		= null;
		}
		
	}
	
	function selectDataSolicitud($table, $where, $id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__'.$table))
			->where($db->quoteName($where) . '=' . $db->quote($id));
		$result = $db->setQuery($query)->loadObjectList();
		
		if(!empty($result)){
			$return = $result[0];
		}else{
			$return = null;
		}
		
		return $return;
	}
}

class IntegradoSimple extends Integrado {
	
	function __construct($integ_id) {
		$this->id = $integ_id;
		$this->usuarios = parent::getUsersOfIntegrado($integ_id);
		
		parent::getSolicitud($integ_id, 0);
	}
	
	function getIntegrado_id($joomla_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__'.$table))
			->where($db->quoteName($where) . '=' . $db->quote($id));
		$result = $db->setQuery($query)->loadObjectList();
		
		if(!empty($result)){
			$return = $result[0];
		}else{
			$return = null;
		}
		
		return $return;
	}
}

