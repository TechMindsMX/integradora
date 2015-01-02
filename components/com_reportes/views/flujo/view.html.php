<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('integradora.integrado');

/**
 * HTML View class for the Reportes Component
 * @property mixed report
 * @property mixed integrado
 */
class ReportesViewFlujo extends JViewLegacy
{
	function __construct() {
		$this->input = JFactory::getApplication()->input;

		parent::__construct();
	}

	// Overwriting JView display method
	function display($tpl = null)
	{
		$sesion = JFactory::getSession();
		$sesIntegId = $sesion->get('integradoId');

		$vars = $this->input->getArray(array('integradoId' => 'INT', 'startDate' => 'STR', 'endDate' => 'STR'));
		$integId = isset($sesIntegId) ? $sesIntegId : $vars['integradoId'];

		$model = $this->getModel();

		// genera el modelo de un reporte nuevo
		$this->report = $model->generateFlujo($vars);

		if (is_null($this->report) ) {
			JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_REPORT_NOT_FOUND'), 'error');
		}

		if (isset($this->report->error)) {
			JFactory::getApplication()->redirect($this->getCancelUrl(), $this->report->error->getMessage(), 'error');
		}

		// verifica el token
//		$sesion->checkToken('get') or JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_ERROR_COD_403'), 'error');

		$integrado = new IntegradoSimple($integId);
		$this->integrado = $integrado->integrados[0];
		$this->integrado->displayName = $integrado->getDisplayName();

		$this->integrado->address = $this->addressFromatted($integId);

		// boton de impresion
		$this->loadHelper('Reportes');

		$url            = 'index.php?com_reportes&view=flujo&integradoId='.$integId.'&inicio='.$this->report->period->fechaInicio->format('d-m-Y').'&fechaFin='.$this->report->period->fechaFin->format('d-m-Y').'&'.JSession::getFormToken(true).'=1';
		$this->printBtn = ReportesHelper::getPrintBtn($url);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}
		// Display the view
		parent::display($tpl);
	}

	private function addressFromatted() {
		// TODO: Llevar este metodo a IntegradoSimple en el refactor
		$sesion = JFactory::getSession();
		$sesIntegId = $sesion->get('integradoId');
		$integId = isset($sesIntegId) ? $sesIntegId : JFactory::getApplication()->input->get('integradoId', null, 'INT');
		$integrado = new IntegradoSimple($integId);

		$integrado = $integrado->integrados[0];

		$postalData = json_decode(file_get_contents(SEPOMEX_SERVICE.$integrado->datos_empresa->cod_postal));
		$coloniaId     = 0; // TODO: quitar mock al traer campo de db
		$postalAddress = $postalData->dTipoAsenta.' '.$postalData->dAsenta[$coloniaId].', '.$postalData->dMnpio.', '.$postalData->dCiudad.', '.$postalData->dEstado;
		$address = $integrado->datos_empresa->calle.' '.$integrado->datos_empresa->num_exterior.' No. Int'.$integrado->datos_empresa->num_interior.', ';

		return $address;
	}
	/**
	 * @return mixed
	 */
	private function getCancelUrl() {
		return 'index.php?option=com_reportes&view=reporteslistados&integradoId='.$this->input->get('integradoId', null, 'INT');
	}


}