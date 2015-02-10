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
	public $integrados;
	
	function __construct($integ_id = null) {
//		$this->user = JFactory::getUser();
//		unset($this->user->password);
		$this->integrados = $this->getIntegradosCurrUser();

		foreach ($this->integrados as $key => $value) {
			$id = $value->integrado_id;
			$this->getSolicitud($id, $key);
		}
	}

    /**
     * @param $integ_id
     * @param $db
     * @return mixed
     */
    public static function getUsuarioPrincipal($integ_id)
    {
        $db 		= JFactory::getDbo();

        $query = $db->getQuery(true);
	    $query->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__integrado_users'))
            ->where($db->quoteName('integrado_id') . ' = ' . $integ_id . ' AND ' . $db->quoteName('integrado_principal') . ' = 1');

        $result = $db->setQuery($query)->loadResult();

        $result = JFactory::getUser($result);
        return $result;
    }

	public static function getSessionIntegradoIdOrRedirectWithError( $instance ) {
		$sesionIntegradoId = JFactory::getSession()->get( 'integradoId', null, 'integrado' );

		$vars = $instance->getQuery( true );
		$uri  = 'index.php?' . str_replace( '&task=' . $vars['task'], '', $instance->getQuery() );

		if ( is_null( $sesionIntegradoId ) ) {
			JFactory::getApplication()->redirect( 'index.php?', 'ERROR_SELECCION_INTEGRADO' );
		} else {
			return $sesionIntegradoId;
		}

	}

	public static function saveBankIfNew( $integradoId ) {
		$respuesta['success'] = false;

		$db   = JFactory::getDbo();
		$save = new sendToTimOne();

		$datosQuery = array ( 'setUpdate' => array () );
		$post       = array (
			'integradoId'       => 'INT',
			'datosBan_id'       => 'INT',
			'db_banco_codigo'   => 'STRING',
			'db_banco_cuenta'   => 'STRING',
			'db_banco_sucursal' => 'STRING',
			'db_banco_clabe'    => 'STRING',
		);

		$data = JFactory::getApplication()->input->getArray( $post );

		// busca los datos bancario por la CLABE
		$table = 'integrado_datos_bancarios';
		if ( empty( $data['db_banco_clabe'] ) ) {
			$data['db_banco_clabe'] = '0000000';
		}
		$where  = $db->quoteName( 'banco_clabe' ) . ' = ' . $data['db_banco_clabe'];
		$existe = getFromTimOne::selectDB( $table, $where );

		$logdata = implode( ', ', array (
			JFactory::getUser()->id,
			$integradoId,
			__METHOD__ . ':' . __LINE__,
			json_encode( $existe )
		) );
		JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

		if ( empty( $existe ) ) {
			$columnas[] = 'integrado_id';
			$valores[]  = $integradoId;

			$datosQuery['columnas'] = $columnas;
			$datosQuery['valores']  = $valores;

			$datosQuery = getFromTimOne::limpiarPostPrefix( $data, 'db_', $datosQuery );

			$validator   = new validador();
			$diccionario = array (
				'db_banco_codigo'   => array ( 'alphaNumber' => true, 'length' => 3, 'required' => true ),
				'db_banco_cuenta'   => array ( 'required' => true ),
				'db_banco_sucursal' => array ( 'required' => true ),
				'db_banco_clabe'    => array ( 'banco_clabe' => $data['db_banco_codigo'], 'length' => 18 )
			);
			$validacion  = $validator->procesamiento( $data, $diccionario );

			if ( $validator->allPassed() ) {
				if ( empty( $existe ) ) {
					$save->insertDB( $table, $datosQuery['columnas'], $datosQuery['valores'] );
					$newId = $db->insertid();
				} else {
					$save->updateDB( $table, $datosQuery['setUpdate'], $where );
				}

				$respuesta['success']        = true;
				$respuesta['banco_codigo']   = $data['db_banco_codigo'];
				$respuesta['banco_cuenta']   = $data['db_banco_cuenta'];
				$respuesta['banco_sucursal'] = $data['db_banco_sucursal'];
				$respuesta['banco_clabe']    = $data['db_banco_clabe'];

				return array ( $respuesta, $existe, $newId, $db, $data, $save );
			} else {
				$logdata = implode( ', ', array (
					JFactory::getUser()->id,
					$integradoId,
					__METHOD__ . ':' . __LINE__,
					json_encode( array ( $validacion, $data['db_banco_clabe'], $data['db_banco_codigo'] ) )
				) );
				JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

				$respuesta['success'] = false;
				$respuesta['msg']     = $validacion;

				return array ( $respuesta, $existe, null, $db, $data, $save );
			}
		} else {
			$respuesta['success'] = false;
			$respuesta['msg'] = array('db_banco_clabe' => array('success' => false,'msg' => 'Esta cuenta ya fue dada de alta') );
		}

		return array ( $respuesta, $existe, null, $db, $data, $save );
	}

	public function getBankName($datos_bancarios){
		$catalogos = new Catalogos();
		$bancos    = $catalogos->getBancos();
		foreach($datos_bancarios as $bancoData){
			foreach ($bancos as $banco) {
				if($banco->claveClabe == $bancoData->banco_codigo){
					$bancoData->bankName = $banco->banco;
				}
			}
		}

		return $datos_bancarios;
	}

	function getIntegrados (){
        $db     =JFactory::getDbo();
        $query  =$db->getQuery(true);
        $query->select('intuser.integrado_id, user.id,user.name' )
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
		
		$query = $db->getQuery(true);
		$query->select($db->quoteName('integrado_id').','.$db->quoteName('integrado_principal').','. $db->quoteName('integrado_permission_level'))
			->from($db->quoteName('#__integrado_users'))
			->where($db->quoteName('user_id') . '=' . $db->quote( JFactory::getUser()->id ));
		$result = $db->setQuery($query)->loadObjectList();

        return $result;
	}
	
	//Retorna todos los usuarios agregados a un Integrado
	public function getUsersOfIntegrado($integ_id){
		if(is_null($integ_id)) { $integ_id = 0; }

		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*')
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
			@$this->integrados[$key]->gral 				= self::selectDataSolicitud('integrado_users', 'user_id', JFactory::getUser()->id);
		}
		$integrado_id 					= isset($this->gral->integrado_id) ? $this->gral->integrado_id : $integ_id;

		if(!is_null($integrado_id) && $integrado_id != 0){
			$this->integrados[$key] = new stdClass();
			$this->integrados[$key]->integrado 			= self::selectDataSolicitud('integrado', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_personales 	= self::selectDataSolicitud('integrado_datos_personales', 'integrado_id', $integrado_id);
			$this->integrados[$key]->datos_empresa 		= self::selectDataSolicitud('integrado_datos_empresa', 'integrado_id', $integrado_id);
            $this->integrados[$key]->params         	= self::selectDataSolicitud('integrado_params', 'integrado_id', $integrado_id);
            $this->integrados[$key]->datos_bancarios	= self::selectDataSolicitud('integrado_datos_bancarios', 'integrado_id', $integrado_id);

			if( !empty($this->integrados[$key]->datos_bancarios) ) {
				$this->integrados[$key]->datos_bancarios = getFromTimOne::getBankName($this->integrados[$key]->datos_bancarios);
			}

			if ( ! empty( $this->integrados[ $key ]->datos_personales->cod_postal ) ) {
				$this->integrados[$key]->datos_personales->direccion_CP = @json_decode(file_get_contents(SEPOMEX_SERVICE.$this->integrados[$key]->datos_personales->cod_postal));
			}

			$empresa = $this->integrados[$key]->datos_empresa;
			if (isset($empresa)){		
				$this->integrados[$key]->testimonio1		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_1);
				$this->integrados[$key]->testimonio2		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->testimonio_2);
				$this->integrados[$key]->poder				= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->poder);
				$this->integrados[$key]->reg_propiedad		= self::selectDataSolicitud('integrado_instrumentos', 'id', $empresa->reg_propiedad);

				if ( !empty( $this->integrados[ $key ]->datos_empresa->cod_postal ) ) {
					$this->integrados[ $key ]->datos_empresa->direccion_CP = @json_decode(file_get_contents(SEPOMEX_SERVICE.$this->integrados[ $key ]->datos_empresa->cod_postal));
				} else {
					$this->integrados[ $key ]->datos_empresa->direccion_CP = 'falta dirección';
				}
			}
		}else{
			$this->integrados[$key]->integrado 			= null;
			$this->integrados[$key]->datos_personales 	= null;
			$this->integrados[$key]->datos_empresa 		= null;
			$this->integrados[$key]->params		 		= null;
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

		if(!is_null($integ_id) ){
            $result = self::getUsuarioPrincipal($integ_id);
			
			if( !is_null($result->id) ){
				$isValid = $result->id==$userJoomla?true:false;
			}
		}
		return $isValid;
	}

	public function belongsToIntegrado( $integrado_id ) {
		$integrados = $this->getIntegradosCurrUser();

		return in_array($integrado_id, $integrados);
	}
}

/**
 * @property null timoneData
 */
class IntegradoSimple extends Integrado {

	protected $ordersAtuhorizationParams;

	function __construct($integ_id) {
        $this->user = JFactory::getUser();

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
		getFromTimOne::selectDB('integrado_params', 'integrado_id');
		$this->ordersAtuhorizationParams = 1;
	}


	public function getDisplayName() {

		if ( isset($this->integrados[0]->datos_empresa->razon_social) && !empty($this->integrados[0]->datos_empresa->razon_social) ) {
			$name = $this->integrados[0]->datos_empresa->razon_social;
		}
		elseif ( isset($this->integrados[0]->datos_personales->nom_comercial) && !empty($this->integrados[0]->datos_personales->nom_comercial) ) {
			$name = $this->integrados[0]->datos_personales->nom_comercial;
		}
		elseif ( isset($this->integrados[0]->datos_personales->nombre_represenante) && isset($this->integrados[0]->datos_personales->nombre_represenante) ) {
			$name = $this->integrados[0]->datos_personales->nombre_represenante;
		}
		else {
			$name = JText::_('LBL_NO_HA_COMPLETADO_SOLICITUD');
		}

		return $name;
	}

	public function setMainAddressFormatted() {
		$codPostal = null;
		$address = null;

		if ( isset( $this->integrados[0]->integrado->pers_juridica ) ) {
			if ($this->integrados[0]->integrado->pers_juridica === '1' && isset($this->integrados[0]->datos_empresa->direccion_CP) ) {
				$postalData     = $this->integrados[0]->datos_empresa->direccion_CP;
				$calle          = $this->integrados[0]->datos_empresa->calle;
				$num_interior   = $this->integrados[0]->datos_empresa->num_interior;
				$num_exterior   = $this->integrados[0]->datos_empresa->num_exterior;
			} elseif ($this->integrados[0]->integrado->pers_juridica === '2' && isset($this->integrados[0]->datos_personales->direccion_CP) ) {
				$postalData     = $this->integrados[0]->datos_personales->direccion_CP;
				$calle          = $this->integrados[0]->datos_personales->calle;
				$num_interior   = $this->integrados[0]->datos_personales->num_interior;
				$num_exterior   = $this->integrados[0]->datos_personales->num_exterior;
			}

			$coloniaId     = 0; // TODO: quitar mock al traer campo de db

			$postalAddress = @$postalData->dTipoAsenta.' '.@$postalData->dAsenta[$coloniaId].', '.@$postalData->dMnpio.', '.@$postalData->dCiudad.', '.@$postalData->dEstado;
			$address = @$calle.' '.@$num_exterior.' No. Int: '.@$num_interior.', '.@$postalAddress;

		}

		$this->integrados[0]->address = $address;
	}

    public function getTimOneData()
    {
	    $this->timoneData = new TimOneData();

        $timoneData = getFromTimOne::selectDB('integrado_timone', 'integradoId = '.$this->id);
		if(!empty($timoneData)) {
			$timoneData = $timoneData[0];

			$uuidTimone = $timoneData->timoneUuid;
			$this->timoneData = getFromTimOne::getTimoneUserDetalis($uuidTimone);
		}
    }

	public function getUserPrincipal() {
		foreach ( $this->usuarios as $user ) {
			if($user->integrado_principal == 1) {
				$response = $user;
			}
		}
		return $response;
	}

	public function canOperate() {
		return $this->integrados[0]->integrado->status == 50;
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
class integrado_params {
}

class UsuarioIntegradora {

	protected $user;

	function __construct() {
		$this->arrayIntIds = $this->setIntegradoIdsCurrentUser();

		$this->user = JFactory::getUser();

	}

	public function isValidIntegradoIdOfCurrentUser( $integ_id ) {
		$valido = in_array($integ_id, $this->arrayIntIds);

		return $valido;
	}

	protected function setIntegradoIdsCurrentUser() {
		$arrayIntIds = array();

		$integrado        = new Integrado();
		$ints = $integrado->getIntegradosCurrUser();

		if ( ! empty( $ints ) ) {
			foreach ( $ints as $int ) {
				$arrayIntIds[] = $int->integrado_id;
			}
		}

		return $arrayIntIds;
	}
}

class TimOneData {
	public $id;
	public $integraUuid;
	public $timoneUuid;
	public $stpClabe;
	public $balance;
}