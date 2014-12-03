<?php
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

class ConciliacionbancoViewDetalle extends JViewLegacy {
	public function display( $tpl = null ) {
        $this->integrados = $this->get('integrados');
		$this->addToolbar();
		parent::display( $tpl );
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/conciliacionbanco.php';
		JToolBarHelper::title( JText::_( 'COM_CONCILIACION_BANCO_DETAIL_TITTLE' ));
	}

}
