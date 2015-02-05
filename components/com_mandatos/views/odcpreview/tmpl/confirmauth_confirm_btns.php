<?php
defined('_JEXEC') or die('Restricted access');

$returnUrl = JRoute::_('index.php?option=com_mandatos&view=odclist');
?>

<legend class="container botones clearfix form-actions">
	<?php
	$statusCorrectos = array(1,3);
	if ($this->permisos['canAuth'] && in_array($this->odc->status->id,$statusCorrectos)):
		$authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odcpreview&task=odcpreview.authorize&idOrden=' . $this->odc->id);
		?>
		<p class="text-warning">
		        <span>
			        <input type="checkbox" class="" id="authorize"/>
                 </span>
			<?php echo JText::_('LBL_CONFIRM_AUTH'); ?>
		</p>
		<a id="authorize-btn" class="btn btn-success esconder"
		   href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
	<?php
	endif;
	?>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
