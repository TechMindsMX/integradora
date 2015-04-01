<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Reportes Component
 */
class ReportesViewReporteslistados extends JViewLegacy
{
    // Overwriting JView display method
    protected $permisos;
    protected $integradoId;

    function display($tpl = null)
    {
        $sesion = JFactory::getSession();
        $this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $this->data             = $this->get('Solicitud');
        $this->balances         = $this->get('BalanceList');
        $this->flujo            = $this->get('Flujo');
        $this->resultados       = $this->get('Resultados');
        $this->projects         = $this->get('Projects');

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

}