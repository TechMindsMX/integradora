<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');

/**
 * Modelo de datos para formulario de seleccion de integrado
 */
class IntegradoModelIntegrado extends JModelItem {
	
	public function getIntegrados()	{
		$integrados = array();
		$integ = new Integrado();

		foreach ( $integ->integrados as $key => $value ) {

			$integrado = new IntegradoSimple( $value->integradoId );

			if ( $integrado->isIntegrado() && $integrado->hasRfc() ) {
				$integrados[$value->integradoId]               = new stdClass();
				$integrados[$value->integradoId]->id           = $integrado->getId();
				$integrados[$value->integradoId]->displayName  = $integrado->getDisplayName();
			}
		}
		return $integrados;
	}
}

