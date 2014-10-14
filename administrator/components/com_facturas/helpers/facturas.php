<?php

/**
 * @version     1.0.0
 * @package     com_facturas
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Nestor Ismael Aguilar Estrada <aguilar_2001@hotmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Facturas helper.
 */
class FacturasHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        		JHtmlSidebar::addEntry(
			JText::_('COM_FACTURAS_TITLE_FACTURAS'),
			'index.php?option=com_facturas&view=facturas',
			$vName == 'facturas'
		);

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
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
