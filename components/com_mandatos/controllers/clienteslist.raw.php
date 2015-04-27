<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerClienteslist extends JControllerAdmin {

    function changeStatus(){
        $document   = JFactory::getDocument();
        $app  = JFactory::getApplication();
        $post       = array('client_id' => 'INT', 'status' => 'INT');
        $data       = $app->input->getArray($post);
        $save       = new sendToTimOne();
	    $session = JFactory::getSession();
	    $data['integradoId'] = $session->get('integradoId', null, 'integrado');

        $result = $save->changeClientOrProviderStatus($data);

        $respose['success'] = $result;
        $respose['msg'] = ($result) ? JText::sprintf('COM_MANDATOS_CLIENT_STATUS_UPDATED', $result['name'], $result['statusName']) : JText::_('LBL_ERROR');

        $document->setMimeEncoding('application/json');

        echo json_encode($respose);
    }
}
