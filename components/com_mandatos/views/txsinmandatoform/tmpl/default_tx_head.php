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
		<span id="name"><?php echo $tx->idTx; ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_DATE') ?></label>
		<span id="name"><?php echo date('d-m-Y', $tx->details->timestamp/1000); ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT') ?></label>
		<span id="name"><?php echo number_format($tx->details->amount,2); ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('LBL_BALANCE') ?></label>
		<span id="name"><?php echo number_format($tx->balance,2); ?></span>
	</div>
</div>
