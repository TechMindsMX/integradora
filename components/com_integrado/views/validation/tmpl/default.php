<?php
defined('_JEXEC') or die('Restricted access');

$submitUrl = JRoute::_('index.php?option=com_integrado&task=finish');
$cancelUrl = JRoute::_('index.php?option=com_integrado');
?>

<form action="<?php echo $submitUrl; ?>" enctype="application/x-www-form-urlencoded" method="post">

	<h2><?php echo JText::_('SOLICITUD_ASK_VALIDATION'); ?></h2>

	<button type="submit" class="btn btn-success" id="finishBtn"><?php echo JText::_('LBL_FIN'); ?></button>
	<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JEXIT'); ?></a>

	<div class="content">
		<?php echo $this->loadTemplate('body'); ?>
	</div>

	<button type="submit" class="btn btn-success" id="finishBtn"><?php echo JText::_('LBL_FIN'); ?></button>
	<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JEXIT'); ?></a>

	<?php echo JHtml::_('form.token'); ?>
</form>

