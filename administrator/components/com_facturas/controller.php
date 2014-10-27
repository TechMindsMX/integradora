<?php
defined('_JEXEC') or die;

jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

class FacturasController extends JControllerLegacy {

    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/facturas.php';
        $view = JFactory::getApplication()->input->getCmd('view', 'facturas');
        JFactory::getApplication()->input->set('view', $view);
        parent::display($cachable, $urlparams);
        return $this;
    }
}
