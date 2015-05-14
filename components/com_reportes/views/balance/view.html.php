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
class ReportesViewBalance extends JViewLegacy
{
	protected $integradoId;

	function __construct() {
		$this->input = JFactory::getApplication()->input;

		parent::__construct();
	}

	// Overwriting JView display method
	function display($tpl = null)
	{
		$vars = $this->input->getArray(array('id' => 'INT'));

		// TODO: recibir en año
		$vars['year'] = 2014;

		$sesion = JFactory::getSession();
		$vars['integradoId'] = $sesion->get('integradoId', null, 'integrado');
        $this->integradoId = $vars['integradoId'];

		$model = $this->getModel();
		$this->report = $model->generateBalance($vars);

		if (is_null($this->report) ) {
			JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_REPORT_NOT_FOUND'), 'error');
		}

		$timezone = new DateTimeZone('America/Mexico_City');
		$fechaFin = new DateTime($this->report->getFechaFin(), $timezone);
		$fechaFin->modify('-1 second');

		$this->report->period->endDate = $fechaFin->format('d-m-Y');

		// verifica el token
//		$sesion->checkToken('get') or JFactory::getApplication()->redirect($this->getCancelUrl(), JText::_('LBL_ERROR_COD_403'), 'error');

		$this->integrado = new IntegradoSimple($vars['integradoId']);

		// boton de impresion
		$this->loadHelper('Reportes');

//		$url            = 'index.php?com_reportes&view=balance&id='.$this->report->id.'1&'.JSession::getFormToken(true).'=1';;
//		$this->printBtn = ReportesHelper::getPrintBtn($url);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

        $this->permisos = Integrado::checkPermisos(__CLASS__, JFactory::getUser()->id, $this->integradoId);
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