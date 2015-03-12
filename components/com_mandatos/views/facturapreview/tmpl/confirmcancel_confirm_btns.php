<?php
defined('_JEXEC') or die('Restricted access');

$returnUrl = JRoute::_('index.php?option=com_mandatos&view=facturalist');
?>

<legend class="container botones clearfix form-actions">
	<?php
	if ($this->permisos['canAuth'] && $this->factura->status->id < 13):
		$authorizeURL = JRoute::_('index.php?option=com_mandatos&view=facturapreview&task=facturapreview.cancel&facturanum=' . $this->factura->getId());
		?>
		<p class="text-warning">
		        <span>
			        <input type="checkbox" class="" id="authorize"/>
                 </span>
			<?php echo JText::_('LBL_CONFIRM_FACTURA_CANCEL'); ?>
		</p>
		<a id="authorize-btn" class="btn btn-success esconder"
		   href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
	<?php
	else :
		?>
		<p class="text-warning"><?php echo JText::_('LBL_FACTURA_PAGADA'); ?></p>
	<?php
	endif;
	?>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
