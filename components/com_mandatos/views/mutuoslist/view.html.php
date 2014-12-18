<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewMutuoslist extends JViewLegacy {
	
	function display($tpl = null){
        $app                  = JFactory::getApplication();
        $this->catalogos      = $this->get('catalogos');
        $this->mutuosAcreedor = $this->get('MutuosAcreedor');
        $this->mutuosDeudor   = $this->get('MutuosDeudor');

        $this->data           = $this->get('Post');

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }
        if($this->data->layout == 'confirmMutuo'){
            $this->get('servicio');
            exit;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->data->integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=clienteslist&integradoId='.$this->data->integradoId;
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}
		
		parent::display($tpl);
	}
}