<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controllerform');
jimport('integradora.integrado');

/**
 * 
 */
class IntegradoControllerIntegrado extends JControllerForm {
	
	public function save($key = null, $urlVar = null)
	{

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$lang  = JFactory::getLanguage();

		$value = JFactory::getApplication()->input->getArray();

		// Create an object for the record we are going to update.
		$object = new stdClass();
		$object->integrado_id = $value['id'];
		$object->status = $value['status'];

		$verified = $this->verified($value);

		$object->datosIntegrado = new IntegradoSimple($object->integrado_id);
		$valido = $this->cambioStatusValido($object->integrado_id, $object->datosIntegrado->integrados[0]->integrado->status, $object->status);
		
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
	
	public function cambioStatusValido($id, $oldStatus, $newStatus)
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

	private function verified($value)
	{
		$verificacion = $value;
		unset($verificacion['id']);
		unset($verificacion['status']);
		unset($verificacion['option']);
		unset($verificacion['task']);
		unset($verificacion['layout']);
		count($verificacion);
		array_pop($verificacion);
		$verificacionObj = json_encode($verificacion);

		$camposVerify = $this->getModel('Integrado')->getCampos();

		$totalCamposVerify = count($camposVerify->LBL_SLIDE_BASIC) +  count($camposVerify->LBL_TAB_EMPRESA) +  count($camposVerify->LBL_TAB_BANCO);

		return count($verificacionObj) == $totalCamposVerify;

	}


}

