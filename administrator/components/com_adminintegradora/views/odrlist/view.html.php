<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewOdrlist extends JViewLegacy {

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        AdminintegradoraHelper::addSubmenu('listadoODR');

        $this->sidebar = JHtmlSidebar::render();
        $this->ordenes = $this->get('Ordenes');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }



    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        JToolBarHelper::title(JText::_('Ordenes de Retiro'), '');

    }

}
