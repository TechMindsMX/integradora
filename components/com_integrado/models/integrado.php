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

			$integrado = new IntegradoSimple($value->integrado_id);

			if ( $integrado->isIntegrado() && $integrado->hasRfc() ) {
				$integrados[$value->integrado_id]               = new stdClass();
				$integrados[$value->integrado_id]->id           = $integrado->getId();
				$integrados[$value->integrado_id]->displayName  = $integrado->getDisplayName();
			}
		}

		return $integrados;
	}
}

