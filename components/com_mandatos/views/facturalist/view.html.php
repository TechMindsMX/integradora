<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewfacturalist extends JViewLegacy {
	protected $integradoId;
	protected $data;

	function display($tpl = null){

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$model = $this->getModel();
		$this->data  = $model->getFacturas( $this->integradoId );

		$this->clients = $this->clientList();

		if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		parent::display($tpl);
	}

	private function clientList() {
        $clients = array();

		foreach ( $this->data as $factura ) {
			if ( isset( $factura->clientName ) ) {
				$clients[$factura->clientId] = $factura->clientName;
			}
		}

		return array_unique($clients);
	}
}