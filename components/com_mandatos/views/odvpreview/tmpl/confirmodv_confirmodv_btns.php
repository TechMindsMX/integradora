<?php
defined('_JEXEC') or die('Restricted access');

$returnUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist&task=cancelOdv&idOrden='.$this->odv->getId());
?>

<legend class="container botones clearfix form-actions">
	<?php
	echo JText::_('LBL_CONFIRM_ORDEN');
	if ($this->permisos['canAuth'] && $this->odv->getStatus()->id == 1):
        $aceptUrl = JRoute::_('index.php?option=com_mandatos&view=odvlist');
		?>
        <a class="btn btn-primary" href="<?php echo $aceptUrl; ?>">Aceptar</a>
	<?php
	endif;
	?>
	<a class="btn btn-danger" href="<?php echo $returnUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
</legend>
