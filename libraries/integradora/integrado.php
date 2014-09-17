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
	
	//retorna todos los integrados (solicitudes) relacionadas al idJoomla
	function getIntegradosCurrUser()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select($db->quoteName('integrado_id').','.$db->quoteName('integrado_principal'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('user_id') . '=' . $db->quote($this->user->id).' AND '.$db->quoteName('integrado_principal').' = 1');
		$result = $db->setQuery($query)->loadObjectList();
		
		$instance->intergrado->ids = $result;
		
		return $result;
	}
	
	//Retorna todos los usuarios agregados a un Integrado
	public function getUsersOfIntegrado($integ_id){
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('integrado_id') . '=' . $integ_id);

		$result = $db->setQuery($query)->loadObjectList();

		foreach ($result as $key => $value) {
			$user = JFactory::getUser($value->user_id);
			
			$user->permission_level		= $value->integrado_permission_level;
			$user->integradoId			= $value->integrado_id;
			$user->integrado_principal	= $value->integrado_principal;
			$user->integrado_rep_legal	= (bool)$value->integrado_rep_legal;

			$result[$key] = $user;

			unset($result[$key]->password);
		}

		return $result;
	}
	
	function separaNombre($value){
	}
	
	function getSolicitud($integ_id = null, $key){
		if ($integ_id == null){
			@$this->integrados[$key]->gral 				= self::selectDataSolicitud('integrado_users', 'user_id', $this->user->id);
		}
		$integrado_id 					= isset($this->gral->integrado_id) ? $this->gral->integrado_id : $integ_id;

		if(!is_null($integrado_id) && $integrado_id != 0){
			$this->integrados[$key]->integrado 			= self::selectDataSolicitud('integrado', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_personales 	= self::selectDataSolicitud('integrado_datos_personales', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_empresa 		= self::selectDataSolicitud('integrado_datos_empresa', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_bancarios	= self::selectDataSolicitud('integrado_datos_bancarios', 'integrado_id', $integrado_id);

			$empresa = $this->integrados[$key]->datos_empresa;
			if (isset($empresa)){		
				$this->integrados[$key]->testimonio1		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_1);
				$this->integrados[$key]->testimonio2		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_2);
				$this->integrados[$key]->poder				= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->poder);
				$this->integrados[$key]->reg_propiedad		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->reg_propiedad);
			}
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
	
	public static function checkPermisos($class, $userId, $integradoId)
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
					->select($db->quoteName(array('lvls_to_edit','lvls_to_auth')))
					->from($db->quoteName('#__integrado_permisos'))
					->where($db->quoteName('view_component') . '=' . $db->quote($class));
		$result = $db->setQuery($query)->loadAssoc();
		
		foreach ($result as $key => $value) {
			$result[$key] = json_decode($value);
		}
		
		// busca el usuario en este integrado
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('integrado_id') . '=' . $integradoId . ' AND '.$db->quoteName('user_id') . '=' .$userId);
		$perm_level = $db->setQuery($query)->loadObject();

		$permisos['canEdit'] = in_array($perm_level->integrado_permission_level, $result['lvls_to_edit'] );
		
		// verifica si puede editar
		$permisos['canAuth'] = in_array($perm_level->integrado_permission_level, $result['lvls_to_auth']);
		
		return $permisos;
		
	}
	public static function getNationalityNameFromId($id) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
					->select($db->quoteName('nombre'))
					->from($db->quoteName('#__catalog_paises'))
					->where('id ='. (int)$id);
		$result = $db->setQuery($query)->loadResult();
		
		return $result;
	}
	
	public static function isValidPrincipal($integ_id, $userJoomla){
		$isValid 	= true;
		$db 		= JFactory::getDbo();
		
		if(!is_null($integ_id) ){
			$query = $db->getQuery(true)
						->select($db->quoteName('user_id'))
						->from($db->quoteName('#__integrado_users'))
						->where($db->quoteName('integrado_id').' = '.$integ_id.' AND '.$db->quoteName('integrado_principal').' = 1');
			
			$result = $db->setQuery($query)->loadResult();
			
			if( !is_null($result) ){
				$isValid = $result==$userJoomla?true:false;
			}
		}
		return $isValid;
	}
}

class IntegradoSimple extends Integrado {
	
	function __construct($integ_id) {
		$this->user = JFactory::getUser();
		
		if( is_null($integ_id) ){
			$integ_id = 0;
		}
		$this->id = $integ_id;
		$this->usuarios = parent::getUsersOfIntegrado($integ_id);
		
		parent::getSolicitud($integ_id, 0);
	}
}

class Autoriza {
	
	public static function __($usuario='')
	{
		$checkUsuario = is_a($usuario, 'JUser');
		//var_dump($checkUsuario);
	}	
}
