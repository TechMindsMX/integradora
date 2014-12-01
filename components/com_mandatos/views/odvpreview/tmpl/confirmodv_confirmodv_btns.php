<?php
defined('_JEXEC') or die('Restricted access');

$returnUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId.'&task=cancelOdv&idOdv='.$this->odv->id);
?>

<legend class="container botones clearfix form-actions">
	<?php
	if ($this->permisos['canAuth'] && $this->odv->status === 0):
        $aceptUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId=' . $this->integradoId);
		?>
        <a class="btn btn-primary" href="<?php echo $aceptUrl; ?>">Aceptar</a>
	<?php
	endif;
	?>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
