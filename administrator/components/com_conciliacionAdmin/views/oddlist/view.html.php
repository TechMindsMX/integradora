<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class conciliacionadminViewOddlist extends JViewLegacy {

    public function display($tpl = null) {
        conciliacionadminHelper::addSubmenu('listadoODD');

        $this->sidebar = JHtmlSidebar::render();
        $this->ordenes = $this->get('Ordenes');
        $this->integrados = $this->get('integrados');


        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/conciliacionadmin.php';
        JToolBarHelper::title(JText::_('Ordenes de Deposito'), '');

    }
}
