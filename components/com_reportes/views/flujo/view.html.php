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

		$vars = $this->input->getArray(array('startDate' => 'STR', 'endDate' => 'STR'));
		$vars['integradoId'] = $sesion->get('integradoId', null, 'integrado');

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
		$sesion->checkToken('get') or JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_ERROR_COD_403'), 'error');

		$this->integrado = new IntegradoSimple($vars['integradoId']);

		// boton de impresion
		$this->loadHelper('Reportes');

		$url            = 'index.php?com_reportes&view=flujo&inicio='.$this->report->getFechaInicio().'&fechaFin='.$this->report->getFechaFin().'&'.JSession::getFormToken(true).'=1';
		$this->printBtn = ReportesHelper::getPrintBtn($url);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

        $this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $vars['integradoId']);
		// Display the view
		parent::display($tpl);
	}

	/**
	 * @return mixed
	 */
	private function getCancelUrl() {
		return 'index.php?option=com_reportes&view=reporteslistados';
	}


}