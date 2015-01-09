<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcpreview extends JViewLegacy {

    protected $integradoId;

    function display($tpl = null){
        $app 				= JFactory::getApplication();

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $this->odc		 	= $this->get('ordenes');
        $this->integCurrent = $this->get('integrado');

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

        $this->loadHelper('Mandatos');

        // Verifica los permisos de edición y autorización
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        parent::display($tpl);
    }
}