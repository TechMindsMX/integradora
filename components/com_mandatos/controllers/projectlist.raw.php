<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProjectlist extends JControllerAdmin {

    function disableProject(){
        $document   = JFactory::getDocument();
        $this->app  = JFactory::getApplication();
        $post       = array('id'          => 'INT');
        $data       = $this->app->input->getArray($post);
        $save       = new sendToTimOne();

        $document->setMimeEncoding('application/json');

        exit;
    }
}
