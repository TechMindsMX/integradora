<?php
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

class adminintegradoraViewConciliacionBancoform extends JViewLegacy {
	public function display( $tpl = null ) {
        require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';

        $post = array(
            'id'            => 'INT',
            'confirmacion'  => 'INT',
            'integradoId'   => 'INT',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $data = JFactory::getApplication()->input->getArray($post);
        $this->integrados           = $this->get('integrados');
        $this->bancos               = $this->get('catalogoBancos');
        $this->bancosIntegradora    = $this->get('BancosIntegradora');
        $this->data                 = (object) $data;

		if (is_numeric($data['integradoId'])) {
			$integ = new IntegradoSimple($data['integradoId']);
			$this->nombreIntegrado = $integ->getDisplayName();
		}

        $this->addToolbar();
		parent::display( $tpl );
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/adminintegradora.php';
		$title = JText::_('COM_CONCICLIACIONBANCO_BTN_DETALLE').' - '. JText::_( 'COM_CONCILIACION_BANCO_DETAIL_TITTLE' );
		JToolBarHelper::title( $title );
	}

}
