<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProjects extends JControllerAdmin {

    function changeStatus(){
        $document   = JFactory::getDocument();
        $app  = JFactory::getApplication();
        $post       = array('id_proyecto' => 'INT', 'status' => 'INT');
        $data       = $app->input->getArray($post);
        $save       = new sendToTimOne();

        $result = $save->changeProjectStatus($data);

        $respose['success'] = $result;
        $respose['msg'] = ($result) ? JText::sprintf('COM_MANDATOS_PROJECTS_STATUS_UPDATED', $result['name'], $result['statusName']) : JText::_('LBL_ERROR');

        $document->setMimeEncoding('application/json');

        echo json_encode($respose);
    }
}
