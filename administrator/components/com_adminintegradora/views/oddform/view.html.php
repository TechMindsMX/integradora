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

        $app  = JFactory::getApplication();;
        $data = $app->input->getArray($post);

        $model = $this->getModel();
        $this->orden     = $model->getOrden();
        $this->integrado = $model->getIntegrado($this->orden->integradoId);
        $this->txs       = AdminintegradoraHelper::getTransacciones($this->orden);

        if (count($this->txs) == 0) {
            $app->enqueueMessage(JText::sprintf('LBL_INTEGRADO_SIN_TXS_A_CONCILIAR', $this->integrado->getDisplayName() ), 'error');
            $app->redirect('index.php?option=com_adminintegradora&view=oddlist');
        }

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
