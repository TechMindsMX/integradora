<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$session = JFactory::getSession();
$integradoId = $session->get('integradoId',null,'integrado');

if( !is_null($integradoId) ) {
    $integrado = new IntegradoSimple($integradoId);

    echo '<div class="pull-right"><h4>'.$integrado->getDisplayName().'</h4></div>';
}