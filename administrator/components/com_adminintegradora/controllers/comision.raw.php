	<?php
defined ('_JEXEC') or die('Restricted Access');

jimport ('joomla.application.component.controlleradmin');
jimport ('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.rutas');
jimport('integradora.notifications');
/**
 *
 */
class AdminintegradoraControllerComision extends JControllerAdmin
{

	protected $envio;

	public function getModel ($name = 'Comision', $prefix = 'AdminintegradoraModel', $config = array ('ignore_request' => true)) {
		$model = parent::getModel ($name, $prefix, $config );

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
		switch ($this->envio['type']){
			case '0':
				// Limpieza de valores que no aplican al type
				$this->envio['rate'] = 0;

				$diccionario = array ('description' 	=> array ('tipo' => 'alfaNum',  'label' => JText::_ ('ERROR_COMISION_DESCRIPTION'),     'length' => 255,    'notNull' => true),
				                      'type' 			=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_TYPE'),            'length' => 10),
				                      'monto' 			=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_MONTO'),           'length' => 10,     'notNull' => true),
				                      'rate' 			=> array ('tipo' => 'float',    'label' => JText::_ ('ERROR_COMISION_RATE'),            'length' => 5),
				                      'frequencyTimes' 	=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_FREQUENCYTIME'),   'length' => 10),
				                      'trigger'	        => array ('tipo' => 'string',   'label' => JText::_ ('ERROR_COMISION_TRIGGER'),         'length' => 255,    'notNull' => true),
				                      'status' 	        => array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_STATUS'),          'length' => 1)
				);
				break;
			case '1':
				// Limpieza de valores que no aplican al type
				$this->envio['monto'] = 0;
				$this->envio['frequencyTimes'] = 0;

				$diccionario = array ('description' 	=> array ('tipo' => 'alfaNum',  'label' => JText::_ ('ERROR_COMISION_DESCRIPTION'),     'length' => 255,    'notNull' => true),
				                      'type' 			=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_TYPE'),            'length' => 10),
				                      'monto' 			=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_MONTO'),           'length' => 10),
				                      'rate' 			=> array ('tipo' => 'float',    'label' => JText::_ ('ERROR_COMISION_RATE'),            'length' => 5,      'notNull' => true),
				                      'frequencyTimes' 	=> array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_FREQUENCYTIME'),   'length' => 10),
				                      'trigger'	        => array ('tipo' => 'string',   'label' => JText::_ ('ERROR_COMISION_TRIGGER'),         'length' => 255,    'notNull' => true),
				                      'status' 	        => array ('tipo' => 'number',   'label' => JText::_ ('ERROR_COMISION_STATUS'),          'length' => 1)
				);
				break;
		}

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
				$this->sendNotifications('nueva');
			} else {
				$result = $request->updateDB('mandatos_comisiones', null, 'id = '.$existe->idExistente );
				$this->sendNotifications('actualizó');
			}

			$sesion = JFactory::getSession();
			$sesion->set('mensaje', 'GUARDADO CORRECTO', 'myMessages');

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
		$comisiones = $request->getComisiones();

		return $comisiones;
	}

	private function cleanStr( $str ) {
		$str = strtolower($str);
		$str = preg_replace('/\s+/', '', $str);

		return $str;
	}

	private function toList ($msg = null, $msgType = 'message') {
		$url = 'index.php?option=com_adminintegradora&view=comisions';
		JFactory::getApplication ()->redirect ($url, $msg, $msgType);

	}

	private function sendNotifications($accion) {
		/*NOTIFICACIONES 23*/
		$data[0] = '<table>';
		$data[2] = '</table>';
		foreach ( $this->envio as $key => $value ) {
			$data[] = '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
		}

		$titulo = JText::_('TITULO_37');

		$contenido = JText::sprintf('NOTIFICACIONES_37', $accion, implode($data) );

		$dato['titulo']         = $titulo;
		$dato['body']           = $contenido;
		$dato['email']          = JFactory::getUser()->email;
		$send                   = new Send_email();
		$info = $send->notification($dato);

		$integradoAdmin     = new IntegradoSimple(93);

		$titulo = JText::_('TITULO_38');

		$contenido = JText::sprintf('NOTIFICACIONES_38', $accion, implode($data) , JFactory::getUser()->username);

		$datoAdmin['titulo']         = $titulo;
		$datoAdmin['body']           = $contenido;
		$datoAdmin['email']          = $integradoAdmin->user->email;
		$send                   = new Send_email();
		$infoAdmin = $send->notification($datoAdmin);

	}

}
