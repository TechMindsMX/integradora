<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewProyectosform extends JViewLegacy {

    protected $integradoId;
    protected $permisos;

    function display($tpl = null){
        $app 				= JFactory::getApplication();
        $post               = array('id_proyecto'=>'INT');
        $data				= $app->input->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        if( !is_null($data['id_proyecto']) ){
            $this->titulo = 'COM_MANDATOS_PROYECTOS_EDICION_PROY_TITULO';
            $this->proyecto = $this->get('Proyecto');
        }else{
            $this->titulo = 'COM_MANDATOS_PROYECTOS_ALTA_PROY_TITULO';
            $proyecto = new stdClass();
            $proyecto->id_proyecto = null;
            $proyecto->integradoId = $this->integradoId;
            $proyecto->parentId = 0;
            $proyecto->name = null;
            $proyecto->description = null;
            $proyecto->status = 1;

            $this->proyecto = $proyecto;
        }

        $this->catalogos = $this->get('Catalogos');

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