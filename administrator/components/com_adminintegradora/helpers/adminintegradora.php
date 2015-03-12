<?php
defined('_JEXEC') or die;

class AdminintegradoraHelper {
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_ODD'),
            'index.php?option=com_adminintegradora&view=oddlist',
            $vName == 'listadoODD'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_ODC'),
            'index.php?option=com_adminintegradora&view=odclist',
            $vName == 'listadoODC'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_ODR'),
            'index.php?option=com_adminintegradora&view=odrlist',
            $vName == 'listadoODR'
        );

        JHtmlSidebar::addEntry(
            JText::_('BTN_GOBACK'),
            'index.php?option=com_adminintegradora',
            $vName == 'Regresar'
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
