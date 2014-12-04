<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class FacturasViewOddform extends JViewLegacy {

    public function display($tpl = null) {

        $this->orden = $this->get('Orden');
        $this->usuarios = $this->get('UserIntegrado');
        $this->integradi = $this->get('Solicitud');
        $this->integrados = $this->get('Integrados');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }



    protected function addToolbar() {
        JToolBarHelper::title(JText::_('Conciliación de Orden de Deposito'), '');

    }

}
