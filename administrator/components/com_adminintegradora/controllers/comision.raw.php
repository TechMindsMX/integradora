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

	protected $envio;

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
		$this->envio = JFactory::getApplication ()->input->getArray (array('description' => 'string',
		                                                                   'type' => 'string',
		                                                                   'monto' => 'string',
		                                                                   'rate' => 'string',
		                                                                   'frequencyTimes' => 'string',
		                                                                   'trigger' => 'string',
		                                                                   'status'  => 'string'
		                                                             ));

		$diccionario = array ('description' 	=> array ('tipo' => 'alfaNum', 'label' => JText::_ ('ERROR_COMISION_DESCRIPTION'), 'length' => 255),
							  'type' 			=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_TYPE'), 'length' => 10),
							  'monto' 			=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_MONTO'), 'length' => 10),
							  'rate' 			=> array ('tipo' => 'float', 'label' => JText::_ ('ERROR_COMISION_RATE'), 'length' => 2),
							  'frequencyTimes' 	=> array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_FREQUENCYTIME'), 'length' => 10),
							  'trigger'	        => array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_STATUS'), 'length' => 1),
							  'status' 	        => array ('tipo' => 'number', 'label' => JText::_ ('ERROR_COMISION_STATUS'), 'length' => 1)
		);

		$validator = new validador();
		$validaResult = $validator->procesamiento($this->envio, $diccionario);

		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		if (validador::noErrors($validaResult)) {

			$request = new sendToTimOne();
			$request->formatData($this->envio);  // Se envian los datos al objeto de ingreso en DB

			$existe = $this->checkExisting();

			if (!$existe->verificacion) {
				$result = $request->insertDB('mandatos_comisiones');
			} else {
				$result = $request->updateDB('mandatos_comisiones', null, 'id = '.$existe->idExistente );
			}

			$sesion = JFactory::getSession();
			$sesion->set('mensaje', 'GUARDADO CORRECTO', 'myNameSpace');

			echo json_encode(array('redirect' => $result));
		} else {
			$validaResult['redirect'] = false;
			echo json_encode($validaResult);
		}

	}

	private function checkExisting() {
		$comisiones = $this->getAllComisions();

		$existe = new stdClass();
		$existe->verificacion = false;
		$existe->idExistente = null;

		$comi = !is_array($comisiones) ? array($comisiones) : $comisiones;
		foreach ( $comi as $k => $v ) {
			if ($this->cleanStr($v->description) == $this->cleanStr($this->envio['description'])) {
				$existe->verificacion = true;
				$existe->idExistente = $v->id;
			}
		}

		return $existe;
	}

	private function getAllComisions() {
		$request = new getFromTimOne();
		$comisiones = $request->getAllComisions();

		return $comisiones;
	}

	private function cleanStr( $str ) {
		$str = strtolower($str);
		$str = preg_replace('/\s+/', '', $str);

		return $str;
	}


}
