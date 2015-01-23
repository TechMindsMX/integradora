<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProductoslist extends JControllerAdmin {

    function changeStatus(){
        $document   = JFactory::getDocument();
        $app  = JFactory::getApplication();
        $post       = array('id_producto' => 'INT', 'status' => 'INT');
        $data       = $app->input->getArray($post);
        $save       = new sendToTimOne();

        $result = $save->changeProductStatus($data);

        $respose['success'] = $result;
        $respose['msg'] = JText::sprintf('COM_MANDATOS_PRODUCTOS_STATUS_UPDATED', $result['name'], $result['statusName']);

        $document->setMimeEncoding('application/json');

        echo json_encode($respose);
    }
}
