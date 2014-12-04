<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class FacturasViewFacturas extends JViewLegacy {

    public function display($tpl = null) {

        $this->facturas = $this->get('Facturas');
        $this->usuarios = $this->get('UserIntegrado');
        $this->integradi = $this->get('Solicitud');
        $this->integrados = $this->get('integrados');

        $this->comision = $this->get('Comision');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }



    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/facturas.php';
        JToolBarHelper::title(JText::_('Facturas'), '');

    }

}
