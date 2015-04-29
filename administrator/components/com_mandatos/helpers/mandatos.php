<?php
defined('_JEXEC') or die;

class MandatosHelper {
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
            JText::_('COM_MANDATOS_LISTADO_ODP'),
            'index.php?option=com_mandatos&view=odplist',
            $vName == 'listadoMutuos'
        );
    }

    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_facturas';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    // esta funcion y el objeto que se maneja en las vistas para proyecto y subproyecto deben adaptarse
    public static function getProyectFromId($proyId, $integradoId){
        $proyKeyId = array();

        $proyectos = getFromTimOne::getProyects($integradoId);

        // datos del proyecto y subproyecto involucrrado
        foreach ( $proyectos as $key => $proy) {
            $proyKeyId[$proy->id_proyecto] = $proy;
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

    public static function valida($input, $diccionario){
        $validacion = new validador();
        $document = JFactory::getDocument();

        $respuesta = $validacion->procesamiento($input, $diccionario);

        $document->setMimeEncoding('application/json');

        return $respuesta;
    }


}
