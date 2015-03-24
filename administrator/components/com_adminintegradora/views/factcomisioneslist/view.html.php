<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewFactcomisioneslist extends JViewLegacy {

    public function display($tpl = null) {
        $this->facturas = $this->get('Facturas');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        JToolBarHelper::title(JText::_('Facturas de Comisiones'), '');

    }

}
