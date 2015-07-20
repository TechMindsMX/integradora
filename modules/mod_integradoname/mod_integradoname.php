<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$class_sfx	= htmlspecialchars($params->get('class_sfx'));
require JModuleHelper::getLayoutPath('mod_integradoname', $params->get('layout', 'default'));
