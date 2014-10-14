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
class MandatosViewFacturapreview extends JViewLegacy {

	function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray();
		$this->integradoId 	= $data['integradoId'];

		$this->factura		= $this->get('facturas');

		$this->integCurrent = $this->get('integrado');

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		$url = 'index.php?option=com_mandatos&view=facturapreview&integradoId=' . $this->integradoId . '&facturanum=' . $data['facturanum'];
		$this->printBtn = MandatosHelper::getPrintButton($url);

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		$app->enqueueMessage('LBL_FACURA_PAGADA', 'message');

		parent::display($tpl);
	}
}