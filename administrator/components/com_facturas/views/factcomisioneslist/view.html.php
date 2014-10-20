<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class FacturasViewFactcomisioneslist extends JViewLegacy {

    public function display($tpl = null) {
        $this->facturas = $this->get('Facturas');
        $this->usuarios = $this->get('UserIntegrado');

        $this->comision = $this->get('Comision');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/facturas.php';
        JToolBarHelper::title(JText::_('Facturas de Comisiones'), '');

    }

}
