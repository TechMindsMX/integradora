<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.gettimone');

/**
 * Modelo de datos para Reporte Balance
 * @property mixed app
 */
class ReportesModelResultados extends JModelItem {

	protected $input;

	function __construct() {
		$this->input = JFactory::getApplication()->input;

		parent::__construct();
	}
}