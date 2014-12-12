<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewMutuospreview extends JViewLegacy {
	
	function display($tpl = null){
        $this->data         = (object) $this->get('DataPost');
        $this->integradoId 	= $this->data->integradoId;
        $this->idMutuo      = $this->data->idMutuo;
        $this->tipoPago     = $this->get('TipoPago');

        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		 //Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}