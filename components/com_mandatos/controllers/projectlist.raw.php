<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProjectlist extends JControllerAdmin {

    function disabledProject(){
        $post       = array('integradoId' => 'INT',
                            'id'          => 'INT');
        $db	        = JFactory::getDbo();
        $document   = JFactory::getDocument();
        $this->app  = JFactory::getApplication();
        $data       = $this->app->input->getArray($post);
        $id         = $data['id'];
        $save       = new sendToTimOne();

        $document->setMimeEncoding('application/json');

        exit;
    }
}
