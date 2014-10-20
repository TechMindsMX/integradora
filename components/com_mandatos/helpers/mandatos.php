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

    public static function getOddListado(){

    }
	
	public static function checkPermisos($viewClass, $integradoId) {
		$user = JFactory::getUser();
		
		$permisos = Integrado::checkPermisos($viewClass, $user->id, $integradoId);
		
		return $permisos;;
	}

	public static function getPrintButton($url)
	{
		// Vista previa de impresion
			$app 		= JFactory::getApplication();
			$document	= JFactory::getDocument();

			$isModal = $app->input->get('print') == 1; // 'print=1' will only be present in the url of the modal window, not in the presentation of the page
			$template = $app->getTemplate();
			if ($isModal) {
				$document->addStyleSheet(JURI::base() . 'templates/' . $template . '/css/bootstrap.css');
				$document->addStyleSheet(JURI::base() . 'templates/' . $template . '/css/override.css');
				$href = '"#" onclick="window.print(); return false;"';
			} else {
				$href = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
				$href = "window.open(this.href,'win2','" . $href . "'); return false;";
				$href = $url. '&tmpl=component&print=1" onclick="' . $href;
			}
			return '<a class="btn btn-default" href="'.$href.'">'.JText::_('LBL_IMPRIMIR').'</a>';
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
