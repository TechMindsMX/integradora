<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvform extends JViewLegacy {

    function display($tpl = null){
        $inputVars 		    = JFactory::getApplication()->input->getArray();
        $this->integradoId  = $inputVars['integradoId'];
        $this->clientes     = $this->get('clientes');
        $this->proyectos    = $this->get('proyectos');
        $this->estados      = $this->get('estados');
        $this->solicitud    = $this->get('datosSolicitud');
        $this->products     = $this->get('productos');
        $this->cuentas      = $this->get('Cuentas');

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }


        parent::display($tpl);
    }
}
