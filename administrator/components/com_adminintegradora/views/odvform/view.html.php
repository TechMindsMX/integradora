<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewOdvform extends JViewLegacy {

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        $post = array(
	        'id'            => 'INT',
            'idTx'          => 'INT',
            'ordenPagada'   => 'INT',
            'cuenta'        => 'INT',
            'confirmacion'  => 'INT',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );

	    $data = JFactory::getApplication()->input->getArray($post);

        $model = $this->getModel();
	    $this->orden     = $model->getOrden();
        $this->integrado = $model->getIntegrado($data['integradoId']);
	    $this->txs       = $model->getTransacciones();
	    $this->data      = (object) $data;

	    if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        JToolBarHelper::title(JText::_('Conciliación de Orden de Venta'), '');
    }

}
