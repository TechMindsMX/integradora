<?php
defined('_JEXEC') or die('Restricted Access');


/**
 * helper class for mandatos component
 */
class MandatosHelper {
	
	function __construct($argument) {
		
	}

	// esta funcion y el objeto que se maneja en las vistas para proyecto y subproyecto deben adaptarse
	public static function getProyectFromId($proyId, $integradoId){
		$proyKeyId = array();
		
		$proyectos = getFromTimOne::getProyects($integradoId);
		
		// datos del proyecto y subproyecto involucrrado
		foreach ( $proyectos as $key => $proy) {
			$proyKeyId[$proy->id] = $proy;
		}
			
		if(array_key_exists($proyId, $proyKeyId)) {
			$proyecto = $proyKeyId[$proyId];
			
			if($proyecto->parentId > 0) {
				$sub_proyecto	= $proyecto;
				$proyecto		= $proyKeyId[$proyecto->parentId];
			} else {
				$subproyecto 	= null;
			}
		}
	}
	
	public static function getProviderFromID($providerId, $integradoId){
		$proveedores = array();
		
		$clientes = getFromTimOne::getClientes($integradoId);
		
		foreach ($clientes as $key => $value) {
			if($value->type == 1){
				$proveedores[$value->id] = $value;
			}
		}
		
		$proveedor = $proveedores[$providerId];
		
		return $proveedor;
	}
	
}
