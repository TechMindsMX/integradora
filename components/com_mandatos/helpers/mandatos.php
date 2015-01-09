<?php

defined('_JEXEC') or die('Restricted Access');

jimport('integradora.integrado');
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

		return $proyecto;
	}
	
	public static function getProviderFromID($providerId, $integradoId){
        $proveedores = getFromTimOne::getClientes($integradoId, 1);

        return $proveedores;
	}

    public static function getOddListado(){

    }
	
	public static function checkPermisos($viewClass, $integradoId) {
		$user = JFactory::getUser();
		
		$permisos = Integrado::checkPermisos($viewClass, $user->id, $integradoId);
		
		return $permisos;
	}

	public static function getPrintButton($url)
	{
		return getFromTimOne::generatePrintButton( $url );
	}

    public static function getClientsFromID($clientId, $integradoId){
        $datos = getFromTimOne::getClientes($integradoId);

        foreach ($datos as $key => $value) {
           if($clientId==$value->id){
               $cliente = $value;
           }
        }

        return $cliente;
    }


}
