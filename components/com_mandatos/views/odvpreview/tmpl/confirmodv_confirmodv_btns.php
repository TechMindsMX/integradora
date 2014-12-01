<?php
defined('_JEXEC') or die('Restricted access');
$aceptUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId);
$returnUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId.'&task=cancelOdv&idOdv='.$this->odv->id);
?>

<legend class="container botones clearfix form-actions">
	<?php
	if ($this->permisos['canAuth'] && $this->odv->status === 0):
		$authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odvpreview&task=odvpreview.authorize&integradoId=' . $this->integradoId . '&odvnum=' . $this->odv->id);
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
    <a class="btn btn-primary" href="<?php echo $aceptUrl; ?>">Aceptar</a>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
