<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewProyectosform extends JViewLegacy {

    function display($tpl = null){
        $app 				= JFactory::getApplication();
        $post               = array('integradoId'=>'INT','id_proyecto'=>'INT');
        $data				= $app->input->getArray($post);

        if( !is_null($data['id_proyecto']) ){
            $this->titulo = 'COM_MANDATOS_PROYECTOS_EDICION_PROY_TITULO';
            $this->proyecto = $this->get('Proyecto');
        }else{
            $this->titulo = 'COM_MANDATOS_PROYECTOS_ALTA_PROY_TITULO';
            $proyecto = new stdClass();
            $proyecto->id_proyecto = null;
            $proyecto->integradoId = $data['integradoId'];
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
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $data['integradoId']);

        if (!$this->permisos['canEdit']) {
            $url = 'index.php?option=com_mandatos&view=proyectoslist&integradoId='.$data['integradoId'];
            $msg = JText::_('JERROR_ALERTNOAUTHOR');
            $app->redirect(JRoute::_($url), $msg, 'error');
        }

        parent::display($tpl);
    }
}