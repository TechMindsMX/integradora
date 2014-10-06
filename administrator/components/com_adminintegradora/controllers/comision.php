<?php
defined ('_JEXEC') or die('Restricted Access');

jimport ('joomla.application.component.controlleradmin');
jimport('integradora.validator');

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
		$url = 'index.php?option=com_adminintegradora&view=comisions';
		JFactory::getApplication ()->redirect ($url);
	}

	public function savecomision () {
		$envio = JFactory::getApplication ()->input->getArray (array('description' => 'string', 'type' => 'string', 'monto' => 'string', 'rate' => 'float', 'frequencyTimes' => 'string'));

		$diccionario = array ('description' 	=> array ('tipo' => 'alfaNum', 'label' => JText::_ ('LBL_INTEGRADO_ID'), 'length' => 255),
							  'type' 			=> array ('tipo' => 'number', 'label' => JText::_ ('LBL_INTEGRADO_ID'), 'length' => 10),
							  'monto' 			=> array ('tipo' => 'number', 'label' => JText::_ ('LBL_INTEGRADO_ID'), 'length' => 10),
							  'rate' 			=> array ('tipo' => 'rate', 'label' => JText::_ ('LBL_INTEGRADO_ID'), 'length' => 10),
							  'frequencyTimes' 	=> array ('tipo' => 'number', 'label' => JText::_ ('LBL_INTEGRADO_ID'), 'length' => 10)
		);

		$validaResult = validador::procesamiento($envio, $diccionario);

		var_dump($validaResult);

	}

}
