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
	// Overwriting JView display method
	function display($tpl = null)
	{
		$sesion = JFactory::getSession();
		$sesIntegId = $sesion->get('integradoId');
		$integId = isset($sesIntegId) ? $sesIntegId : JFactory::getApplication()->input->get('integradoId', null, 'INT');

        $this->report  = $this->get('balance');

		$integrado = new IntegradoSimple($integId);
		$this->integrado = $integrado->integrados[0];
		$this->integrado->displayName = $integrado->getDisplayName();

		$this->integrado->address = $this->addressFromatted($integId);



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
		$postalAddress = $postalData->dTipoAsenta.' '.$postalData->dAsenta[0].
		$address = $integrado->datos_empresa->calle.' '.$integrado->datos_empresa->num_exterior.' No. Int'.$integrado->datos_empresa->num_interior.', ';
		var_dump($address, $postalData);exit;

		return $address;
	}

}