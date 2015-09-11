<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div id="exchange-rate-module" class="<?php echo $moduleclass_sfx; ?> bg-warning box pull-left span3">
        <div class="row-fluid">
            <span class="span6">
                <?php echo JText::_('EXCH_RATE_DATE'); ?>
            </span>
            <span class="span6 text-right">
                <?php echo $exrate->fecha_tc; ?>
            </span>
        </div>

        <div class="row-fluid">
            <span class="span6">
                <?php echo JText::_('EXCH_RATE'); ?>
            </span>
            <span class="span6 text-right">
                <?php echo $exrate->tc; ?> MXN/USD
            </span>
        </div>
        <div class="text-center"><small><i><?php echo JText::_('EXCH_RATE_LEGEND'); ?></i></small></div>
    </div>
</div>
