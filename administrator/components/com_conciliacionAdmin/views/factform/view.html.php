<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class conciliacionadminViewFactform extends JViewLegacy {

    public function display($tpl = null) {

        $this->factura = $this->get('Factura');
        $this->usuarios = $this->get('UserIntegrado');
        $this->numcuentas = $this->get('NumCuentas');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }



    protected function addToolbar() {
        JToolBarHelper::title(JText::_('Conciliación de Facturas'), '');

    }

}
