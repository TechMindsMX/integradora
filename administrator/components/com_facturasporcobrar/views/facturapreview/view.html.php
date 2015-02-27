<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * @property mixed integradoId
 * @property mixed factura
 * @property mixed integCurrent
 * @property mixed permisos
 * @property mixed printBtn
 */
class FacturasporcobrarViewFacturapreview extends JViewLegacy {

	function display($tpl = null){



		$app 				= JFactory::getApplication();

       // echo '<pre>';
        $data				= $app->input->getArray();

		$this->integradoId 	= $data['integradoId'];

		$this->factura		= $this->get('facturas');

		$this->integCurrent = $this->get('integrado');


        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

		$this->loadHelper('Preview');

		$this->factura->proyecto    = PreviewHelper::getProyectFromId($this->factura->projectId, $this->integradoId);

		$this->factura->proveedor   = PreviewHelper::getClientsFromID($this->factura->clientId, $this->integradoId);

		// Boton de impresion
		$url = 'index.php?option=com_facturasporcobrar&view=facturapreview&integradoId=' . $this->integradoId . '&facturanum=' . $data['facturanum'];
		$this->printBtn = PreviewHelper::getPrintButton($url);

		parent::display($tpl);
	}
}