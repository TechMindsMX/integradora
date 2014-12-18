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
}
