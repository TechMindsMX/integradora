<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AdminintegradoraViewtxsform extends JViewLegacy {

    public $input;

    public function display($tpl = null) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        AdminintegradoraHelper::addSubmenu('listadotxni');
        $data                    = array('idtx'=>'INT', 'integradoId'=>'STRING');
        $this->sidebar           = JHtmlSidebar::render();
        $this->txs               = $this->get('txNoIdent');
        $this->integrados        = $this->get('integrados');
        $this->input             = JFactory::getApplication()->input->getArray($data);

        if(!is_null($this->input['integradoId'])) {
            $this->datosConfirmacion = $this->get('confirmacion');
        }


        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
        JToolBarHelper::title(JText::_('COM_TXS_CONCILIACION'), '');

    }
}
