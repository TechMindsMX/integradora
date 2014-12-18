<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

class MandatosViewMandatos extends JViewLegacy {

    protected $items;

    protected $pagination;

    protected $state;

    function display($tpl = null) {
        $this->mutuos = $this->get('Mutuos');

        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar(){
        JToolBarHelper::title(JText::_('Listado de Mutuos'), '');
    }
}
