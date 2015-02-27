<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

class MandatosViewOdplist extends JViewLegacy {

    protected $items;

    protected $pagination;

    protected $state;

    function display($tpl = null) {
        $this->odps = $this->get('Odps');

        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar(){
        JToolBarHelper::title(JText::_('Listado de Ordenes de Prestamo'), '');
    }
}
