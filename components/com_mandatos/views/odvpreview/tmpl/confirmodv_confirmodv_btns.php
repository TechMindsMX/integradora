<?php
defined('_JEXEC') or die('Restricted access');

$returnUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId.'&task=cancelOdv&idOrden='.$this->odv->id);
?>

<legend class="container botones clearfix form-actions">
	<?php
	echo JText::_('LBL_CONFIRM_ORDEN');
	if ($this->permisos['canAuth'] && $this->odv->status->id === 0):
        $aceptUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId);
		?>
        <a class="btn btn-primary" href="<?php echo $aceptUrl; ?>">Aceptar</a>
	<?php
	endif;
	?>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
