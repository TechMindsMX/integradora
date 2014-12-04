<?php
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

class ConciliacionbancoViewDetalle extends JViewLegacy {
	public function display( $tpl = null ) {
        $post = array(
            'id'            => 'int',
            'confirmacion'  => 'INT',
            'integradoId'   => 'INT',
            'cuenta'        => 'STRING',
            'referencia'    => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $data = JFactory::getApplication()->input->getArray($post);
        $this->integrados   = $this->get('integrados');
        $this->bancos       = $this->get('catalogoBancos');
        $this->data         = (object) $data;

        $this->addToolbar();
		parent::display( $tpl );
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/conciliacionbanco.php';
		JToolBarHelper::title( JText::_( 'COM_CONCILIACION_BANCO_DETAIL_TITTLE' ));
	}

}
