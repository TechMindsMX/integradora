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
    <div id="exchange-rate-module" class="<?php echo $moduleclass_sfx; ?> bg-warning box pull-left">
        <div class="row-fluid">
            <span class="span6">
                <?php echo JText::_('LBL_TOTAL'); ?>
            </span>
            <span class="span6 text-right">
                <?php echo number_format($balances['total'], 2); ?> MXN
            </span>
        </div>

        <div class="row-fluid">
            <span class="span6">
                <?php echo JText::_('LBL_BLOCKED'); ?>
            </span>
            <span class="span6 text-right">
                <?php echo number_format($balances['blocked'], 2); ?> MXN
            </span>
        </div>

        <div class="row-fluid">
            <span class="span6">
                <?php echo JText::_('LBL_AVAILABLE'); ?>
            </span>
            <span class="span6 text-right">
                <?php echo number_format($balances['available'], 2); ?> MXN
            </span>
        </div>

    </div>
</div>
