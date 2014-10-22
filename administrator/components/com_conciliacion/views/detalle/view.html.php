<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 22-Oct-14
 * Time: 3:52 PM
 */

// No direct access
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );


class ConciliacionViewDetalle extends JViewLegacy {
	public $item;
	public $odv;
	public $odc;
	public $odr;
	public $odd;

	/**
	 * Display the view
	 */
	public function display( $tpl = null ) {

		$this->item = $this->get( 'TxSTPById' );
		$this->odv  = $this->get( 'odvs' );
		$this->odc  = $this->get( 'odcs' );
		$this->odd  = $this->get( 'odds' );
		$this->odr  = $this->get( 'odrs' );

		$this->addToolbar();
		parent::display( $tpl );
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/conciliacion.php';
		JToolBarHelper::title( JText::_( 'Conciliacion STP' ),
		                       '' );


	}

}
