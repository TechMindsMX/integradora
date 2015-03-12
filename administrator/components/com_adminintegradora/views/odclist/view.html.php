<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewOdclist extends JViewLegacy {

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        AdminintegradoraHelper::addSubmenu('listadoODC');

        $this->sidebar = JHtmlSidebar::render();
        $this->ordenes = $this->get('Ordenes');
        $this->usuarios = $this->get('UserIntegrado');
        $this->integradi = $this->get('Solicitud');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }



    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        JToolBarHelper::title(JText::_('Ordenes de Compra'), '');
    }

}
