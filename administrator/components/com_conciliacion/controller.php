<?php

// No direct access
defined('_JEXEC') or die;

class ConciliacionController extends JControllerLegacy {

    public function display($cachable=null, $urlparams=null) {


        $view = JFactory::getApplication()->input->getCmd('view', 'conciliacion');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;

    }
    function __construct() {
        parent::__construct();
    }


}
