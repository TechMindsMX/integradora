<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controllerform');
jimport('integradora.integrado');

/**
 *
 */
class IntegradoControllerIntegrado extends JControllerForm {

	protected $data;
	protected $integradoId;
	private $tabla_db;

	function __construct( ) {
		$this->data = JFactory::getApplication()->input->getArray();
		$this->tabla_db = 'integrado_verificacion_solicitud';
		$this->save = new sendToTimOne();

		$this->integradoId = $this->data['id'];

		parent::__construct();
	}

	public function save($key = null, $urlVar = null)
	{

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$lang  = JFactory::getLanguage();

		$result = $this->saveVerifications();

		// Create an object for the record we are going to update.
		$object = new stdClass();
		$object->integrado_id = $this->data['id'];
		$object->status = $this->data['status'];

		$verified = $this->hasAllVerifications();

		$datosIntegrado = new IntegradoSimple($object->integrado_id);
		$valido = $this->cambioStatusValido( $datosIntegrado->integrados[0]->integrado->status, $object->status);

		if (!$valido || !$verified) {
			$this->setMessage(JText::_('JERROR_VALIDACION_STATUS'),'error');
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($object->integrado_id, 'id' ) , false
				)
			);
			return true;
		}

		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__integrado', $object, 'integrado_id');

		$this->setMessage(
			JText::_('JLIB_APPLICATION' . '_SUBMIT'  . '_SAVE_SUCCESS')
		);

		// Redirect to the list screen.
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		return true;
	}

	public function cambioStatusValido($oldStatus, $newStatus)
	{
		$catalogos = $this->getCatalogos();
		switch (intval($oldStatus)) {
			case 0: // Nueva solicitud
				$validos = array(2,3,99);
				break;
			case 1: // para revision nuevamente
				$validos = array(2,3,99);
				break;
			case 2: // Devuelto
				$validos = array(1);
				break;
			case 3: // contrato
				$validos = array(50,99);
				break;
			case 50: // integrado
				$validos = array();
				break;
			case 99: // cancelada
				$validos = array();
				break;
			default:
				$validos = array();
				break;
		}

		return (in_array($newStatus, $validos)) ? true : false ;
	}

	public function getCatalogos() {
		$catalogos = new Catalogos;

		$catalogos->getStatusSolicitud();

		return $catalogos;
	}

	private function hasAllVerifications()
	{

		$verificacionObj = $this->groupVerifications();

		$countVerifObj = 0;
		foreach ( $verificacionObj as $key => $val ) {
			$countVerifObj = count($val) + $countVerifObj;
		}

		$camposVerify = $this->getModel('Integrado')->getCampos();

		$totalCamposVerify = count($camposVerify->LBL_SLIDE_BASIC) +  count($camposVerify->LBL_TAB_EMPRESA) +  count($camposVerify->LBL_TAB_BANCO);

		return $countVerifObj == $totalCamposVerify;

	}

	private function groupVerifications() {
		$valores = null;

		$verificacion = $this->data;
		unset($verificacion['id']);
		unset($verificacion['status']);
		unset($verificacion['option']);
		unset($verificacion['task']);
		unset($verificacion['layout']);
		unset($verificacion['view']);
		count($verificacion);
		array_pop($verificacion);

		foreach ( $verificacion as $key => $value ) {
			$keyLimpia = $this->explodeX(array('integrado_datos_personales_', 'integrado_datos_empresa_','integrado_datos_bancarios_'), $key);
			$valores[$keyLimpia->table][$keyLimpia->key] = $value;
		}

		return $valores;
	}

	function explodeX( $delimiters, $string )
	{
		$val = new stdClass();

		foreach ( $delimiters as $key => $value ) {
			if ( strstr($string, $value) ){
				$val->delimiter = $value;
				$val->table =  str_replace('integrado_', '', substr($val->delimiter, 0 ,-1));
				$val->key    = str_replace( $val->delimiter , '', $string);
			}
		}

		return $val;
	}

	private function saveVerifications() {

		$retorno = null;

		$data = $this->groupVerifications();

		$this->checkExistIntegrado();

		if(empty($this->_errors)) {
			if ( isset( $data ) ) {
				foreach ( $data as $tabla => $campos ) {
					$set[$tabla]       = json_encode( $campos ) ;
				}
			}
			$set['integradoId'] = $this->integradoId;

			$condition = 'integradoId = ' . $this->integradoId;
			$this->save->deleteDB($this->tabla_db, $condition);

			$this->save->formatData($set);

			$update = $this->save->insertDB($this->tabla_db);

			if ( $update ) {
				$retorno = getFromTimOne::selectDB( $this->tabla_db, $condition );
				$retorno = $retorno[0];
			} else {
				$retorno = false;
			}

		}

		return $retorno;
	}

	private function checkExistIntegrado() {

		$integrado = getFromTimOne::selectDB($this->tabla_db, 'integradoId ='. $this->data['id']);

		if(empty($integrado)) {
			$this->save->formatData(array('integradoId' => $this->data['id']));
			$result = $this->save->insertDB($this->tabla_db);

			if(!$result) {
				$this->_errors = true;
			}
		}
	}
}


