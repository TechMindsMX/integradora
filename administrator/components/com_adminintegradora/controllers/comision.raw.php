	<?php
defined ('_JEXEC') or die('Restricted Access');

jimport ('joomla.application.component.controlleradmin');
jimport ('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.rutas');
/**
 *
 */
class AdminintegradoraControllerComision extends JControllerAdmin
{

	public function getModel ($name = 'Comision',
							  $prefix = 'AdminintegradoraModel') {
		$model = parent::getModel ($name,
								   $prefix,
								   array ('ignore_request' => true));
		return $model;
	}

	public function cancel () {
		$this->toList(JText::_('LBL_CANCELED_OPERATION', 'notice'));
	}

	public function savecomision () {
		$envio = JFactory::getApplication ()->input->getArray (array('description' => 'string', 'type' => 'string', 'monto' => 'string', 'rate' => 'string', 'frequencyTimes' => 'string'));

		$diccionario = array ('description' 	=> array ('tipo' => 'alfaNum', 'label' => JText::_ ('ERROR_COMISION_DESCRIPTION'), 'length' => 255),
							  'type' 			=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_TYPE'), 'length' => 10),
							  'monto' 			=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_MONTO'), 'length' => 10),
							  'rate' 			=> array ('tipo' => 'float', 'label' => JText::_ ('ERROR_COMISION_RATE'), 'length' => 10),
							  'frequencyTimes' 	=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_FREQUENCYTIME'), 'length' => 10)
		);

		$validator = new validador();
		$validaResult = $validator->procesamiento($envio, $diccionario);

		if (validador::noErrors($validaResult)) {
			$request = new sendToTimOne();
			$serviceUrl = new servicesRoute();

			$request->setServiceUrl($serviceUrl->comisionUrls());
			$request->setJsonData(json_encode($envio));
			$request->setHttpType('POST');

			$request->to_timone(); // realiza el envio

			$this->toList();
		} else {
			$document = JFactory::getDocument();
			$document->setMimeEncoding('application/json');
			echo json_encode($validaResult);
		}

	}

	private function toList ($msg = null, $msgType = 'message') {
		$url = 'index.php?option=com_adminintegradora&view=comisions';
		JFactory::getApplication ()->redirect ($url, $msg, $msgType);

	}


}
