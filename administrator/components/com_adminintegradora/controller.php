<?php
defined('_JEXEC') or die('Restricted Access');

jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.rutas');

/**
 * 
 */
class AdminintegradoraController extends JControllerLegacy {
	
	function display($cacheable = false, $urlparams = false) {
		
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'Adminintegradora'));
		
		parent::display($cacheable);
		
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
			$serviceUrl = new IntRoute();

			$request->setServiceUrl($serviceUrl->saveComisionServiceUrl());
			$request->setJsonData($envio);

			$request->to_timone(); // realiza el envio

		} else {
			$document = JFactory::getDocument();
			$document->setMimeEncoding('application/json');
			echo json_encode($validaResult);
		}

	}




}
