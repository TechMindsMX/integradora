<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewSubproyectosform extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray();
		$this->integradoId 	= $data['integradoId'];
		$this->token 		= getFromTimOne::token();
		
		if( isset($data['proyId']) ){
			$this->titulo = 'COM_MANDATOS_PROYECTOS_EDICION_SUB_TITULO';
			$this->data = $this->get('proyectos');
			$this->proyecto = $this->get('proyecto');
		}else{
			$this->titulo = 'COM_MANDATOS_PROYECTOS_ALTA_SUB_TITULO';
			$this->data = $this->get('proyectos');
		}
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=proyectoslist&integradoId='.$this->integradoId;
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}

		parent::display($tpl);
	}
}