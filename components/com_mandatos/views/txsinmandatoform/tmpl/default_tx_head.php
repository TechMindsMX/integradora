<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 15-Dec-14
 * Time: 3:07 PM
 */

defined('_JEXEC') or die('Restricted access');

$tx      	= $this->data[0];
?>
<div style="background-color: #eeeeee; padding: 2em;">
	<h3><?php echo JText::_('COM_MANDATOS_LIST_TX_DATA'); ?></h3>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_REF') ?></label>
		<span id="name"><?php echo $tx->referencia; ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_DATE') ?></label>
		<span id="name"><?php echo $tx->date; ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT') ?></label>
		<span id="name"><?php echo number_format($tx->amount,2); ?></span>
	</div>
</div>
