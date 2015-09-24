<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewtxslist extends JViewLegacy {

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        AdminintegradoraHelper::addSubmenu('listadoODD');

        $this->sidebar = JHtmlSidebar::render();
        $this->txs = $this->get('txNoIdent');
        $this->integrados = $this->get('integrados');


        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        JToolBarHelper::title(JText::_('Ordenes de Deposito'), '');

    }
}
