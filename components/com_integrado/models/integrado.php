<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');


jimport('integradora.integrado');
/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class IntegradoModelIntegrado extends JModelItem {
	
	protected $data;
	
	public function getDisplay()
	{
		if (!isset($this->data)) {
			$this->data = 'Mensaje desde model Integrado';
		}
		return $this->data;
	}
}

