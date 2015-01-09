<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewSubproyectosform extends JViewLegacy {

	protected $integradoId;
	protected $permisos;

	function display($tpl = null){
		$app 				= JFactory::getApplication();
        $post               = array('id_proyecto'=>'INT');
        $data				= $app->input->getArray($post);

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		if( !is_null($data['id_proyecto']) ){
			$this->titulo = 'COM_MANDATOS_PROYECTOS_EDICION_SUB_TITULO';
			$this->data = $this->get('proyectos');
			$this->proyecto = $this->get('proyecto');
		}else{
			$this->titulo = 'COM_MANDATOS_PROYECTOS_ALTA_SUB_TITULO';
			$this->data = $this->get('proyectos');

            $proyecto = new stdClass();
            $proyecto->id_proyecto = null;
            $proyecto->integradoId = $this->integradoId;
            $proyecto->parentId = 0;
            $proyecto->name = null;
            $proyecto->description = null;
            $proyecto->status = 0;

            $this->proyecto = $proyecto;
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
			$url = 'index.php?option=com_mandatos&view=proyectoslist';
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}

		parent::display($tpl);
	}
}