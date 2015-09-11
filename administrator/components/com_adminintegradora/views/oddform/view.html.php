<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewOddform extends JViewLegacy {

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        $post = array(
            'idTx'         => 'INT',
            'ordenPagada'  => 'INT',
            'cuenta'       => 'INT',
            'referencia'   => 'STRING',
            'date'         => 'STRING',
            'amount'       => 'FLOAT',
            'confirmacion' => 'INT'
        );

        $data = JFactory::getApplication()->input->getArray($post);

        $this->orden        = $this->get('Orden');
        $this->integrado    = $this->get('Integrado');
        $this->txs          = $this->get('Transacciones');
        $this->data         = (object) $data;

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
