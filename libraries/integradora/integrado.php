<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');
jimport('integradora.gettimone');


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
		
		unset($this->user->password);

	}

	function getIntegrados (){
        $db     =JFactory::getDbo();
        $query  =$db->getQuery(true);
        $query
            ->select('intuser.integrado_id, user.id,user.name' )
            ->from('#__integrado_users as intuser')
            ->join('INNER', '#__users as user on  intuser.user_id = user.id')
            ->where('intuser.integrado_principal'.' <> 0 ');
        $db->setQuery($query);
        $result=$db->loadObjectList();

        return $result;
    }
	
	//retorna todos los integrados (solicitudes) relacionadas al idJoomla
	function getIntegradosCurrUser()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select($db->quoteName('integrado_id').','.$db->quoteName('integrado_principal').','. $db->quoteName('integrado_permission_level'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('user_id') . '=' . $db->quote($this->user->id));
		$result = $db->setQuery($query)->loadObjectList();

        return $result;
	}
	
	//Retorna todos los usuarios agregados a un Integrado
	public function getUsersOfIntegrado($integ_id){
		if(is_null($integ_id)) { $integ_id = 0; }

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

			$result[$key] = $user;

			unset($result[$key]->password);
		}

		return $result;
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

			if ( ! empty( $this->integrados[ $key ]->datos_personales ) ) {
				$this->integrados[$key]->datos_personales->direccion_CP = json_decode(file_get_contents(SEPOMEX_SERVICE.$this->integrados[$key]->datos_personales->cod_postal));
			}

			$empresa = $this->integrados[$key]->datos_empresa;
			if (isset($empresa)){		
				$this->integrados[$key]->testimonio1		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_1);
				$this->integrados[$key]->testimonio2		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_2);
				$this->integrados[$key]->poder				= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->poder);
				$this->integrados[$key]->reg_propiedad		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->reg_propiedad);

				if ( isset( $this->integrados[ $key ]->datos_empresa->cod_postal ) ) {
					$this->integrados[$key]->datos_empresa->direccion_CP = json_decode(file_get_contents(SEPOMEX_SERVICE.$this->integrados[$key]->datos_empresa->cod_postal));
				}
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
		$className = ($table != 'integrado') ? $table : 'stdClass';

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__'.$table))
			->where($db->quoteName($where) . '=' . $db->quote($id));
		$result = $db->setQuery($query)->loadObjectList('', $className);

		if(!empty($result)){
            if($table == 'integrado_datos_bancarios'){
                $return = $result;
            }else {
                $return = $result[0];
            }
		}else{
			$return = null;
		}
		
		return $return;
	}
	
	public static function checkPermisos($class, $userId, $integradoId)
	{
        $app = JFactory::getApplication();

        if( !isset($integradoId) ){
            $app->redirect('index.php?option=com_mandatos', JText::_('ERROR_NO_SELECCION_INTEGRADO'), 'error');
        }

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('*')
					->from($db->quoteName('#__integrado_permisos'));
		$result = $db->setQuery($query)->loadObjectList();

		foreach ($result as $key => $value) {
			if($value->view_component === 'default' OR $value->view_component === $class) {
				$lvls['lvls_to_edit'] = json_decode($value->lvls_to_edit);
				$lvls['lvls_to_auth'] = json_decode($value->lvls_to_auth);
			}
		}

		// busca el usuario en este integrado
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('integrado_id') . '=' . $integradoId . ' AND '.$db->quoteName('user_id') . '=' .$userId);
		$perm_level = $db->setQuery($query)->loadObject();

		// si el usurio no pertenece al integrado se redirecciona // debe entrar en el log de eventos
		if(is_null($perm_level)) {
			$app->redirect('index.php?option=com_content&view=article&id=8&Itemid=101', JText::_('LBL_SECURITY_PROBLEM'), 'error');
		}

		$permisos['canEdit'] = in_array($perm_level->integrado_permission_level, $lvls['lvls_to_edit'] );

		// verifica si puede editar
		$permisos['canAuth'] = in_array($perm_level->integrado_permission_level, $lvls['lvls_to_auth']);

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

	protected $ordersAtuhorizationParams;

	function __construct($integ_id) {
		$this->user = JFactory::getUser();

//		if( is_null($integ_id) ){
//			$integ_id = 0;
//		}
		$this->id = $integ_id;
		$this->usuarios = parent::getUsersOfIntegrado($integ_id);

		parent::getSolicitud($integ_id, 0);

		$this->setOrdersAtuhorizationParams();

		$this->setMainAddressFormatted();
	}

	/**
	 * @return mixed
	 */
	public function getOrdersAtuhorizationParams() {
		return $this->ordersAtuhorizationParams;
	}

	/**
	 * @param mixed $ordersAtuhorizationParams
	 */
    //TODO quitar simulación de datos.
	public function setOrdersAtuhorizationParams( ) {
		getFromTimOne::selectDB('integrado_params', 'integradoId');
		$this->ordersAtuhorizationParams = 1;
	}


	public function getDisplayName() {

		@$name = isset($this->integrados[0]->datos_empresa->razon_social) ? $this->integrados[0]->datos_empresa->razon_social : $this->integrados[0]->datos_personales->nombre_represenante;

		return $name;
	}

	public static function isValidIntegradoId( $integ_id ) {
		$db =& JFactory::getDbo();
		$integradosRegistrados = getFromTimOne::selectDB('integrado','integrado_id = '.$db->quote($integ_id) );

		return !empty($integradosRegistrados);
	}

	public function setMainAddressFormatted() {
		$codPostal = null;
		$address = null;

		if ( isset( $this->integrados[0]->integrado->pers_juridica ) ) {
			if ($this->integrados[0]->integrado->pers_juridica === '1') {
				$postalData = $this->integrados[0]->datos_empresa->direccion_CP;
			} elseif ($this->integrados[0]->integrado->pers_juridica === '2') {
				$postalData = $this->integrados[0]->datos_personales->direccion_CP;
			}

			$coloniaId     = 0; // TODO: quitar mock al traer campo de db

			$postalAddress = $postalData->dTipoAsenta.' '.$postalData->dAsenta[$coloniaId].', '.$postalData->dMnpio.', '.$postalData->dCiudad.', '.$postalData->dEstado;
			$address = $this->integrados[0]->datos_empresa->calle.' '.$this->integrados[0]->datos_empresa->num_exterior.' No. Int: '.$this->integrados[0]->datos_empresa->num_interior.', '.$postalAddress;

		}

		$this->integrados[0]->address = $address;
	}

}

class integrado_datos_personales {
}
class integrado_datos_empresa {
}
class integrado_datos_bancarios {
}
class integrado_instrumentos {
}
class integrado_users {
}
