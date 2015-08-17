<?php
defined('_JEXEC') or die('Restricted Access');

class IntegradoControllerValidation extends JControllerLegacy {

	/**
	 * IntegradoControllerValidation constructor.
	 */
	public function __construct() {


		$model = $this->getModel('solicitud');

		$this->data = $model->getSolicitud();
		$this->catalogos = $model->getCatalogos();

		$msg = 'LBL_DATA_VALIDATION_INTEGRADO_MISSING';

		if ( isset( $this->integradoId ) ) {
			$integrado = new IntegradoSimple($this->integradoId);

			if ( $integrado->hasAllDataForValidation() ) {
				$msg = 'LBL_HAS_ALL_FOR_VALIDATION';
			}
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_($msg) );
		parent::__construct();
	}
}