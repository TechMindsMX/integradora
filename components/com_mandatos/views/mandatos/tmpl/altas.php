<?php
defined('_JEXEC') or die('Restricted access');

?>

<div class="container">
	<div class="">
		<h2 class="item-title"><?php echo JText::_('LBL_ALTAS'); ?></h2>

		<div class="row-fluid">
			<h3><?php echo JText::_('LBL_CLIENTES'); ?></h3>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clientesform'); ?>" >
					<i class="icon-thumbs-up icon-large"></i><br /><br /><?php echo JText::_('LBL_CLIENTES_PROVEEDORES_NEW'); ?>
				</a>
			</div>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clienteslist'); ?>" <?php if ($this->alta->cliprov->count == 0) : echo 'disabled'; endif; ?>>
					<i class="icon-list icon-large"></i><br /><br /><?php echo JText::_('LBL_CLIENTES_PROVEEDORES_LIST'); ?>
				</a>
			</div>
		</div>

		<br class="clearfix">
		<div class="row-fluid">
			<h3><?php echo JText::_('LBL_PRODUCTS'); ?></h3>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productosform'); ?>" >
					<i class="icon-thumbs-up icon-large"></i><br /><br /><?php echo JText::_('LBL_PRODUCT_NEW'); ?>
				</a>
			</div>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productoslist'); ?>" <?php if ($this->alta->product->count == 0) : echo 'disabled'; endif; ?>>
					<i class="icon-list icon-large"></i><br /><br /><?php echo JText::_('LBL_PRODUCT_LIST'); ?>
				</a>
			</div>
		</div>

		<br class="clearfix">
		<div class="row-fluid">
			<h3><?php echo JText::_('LBL_PROJECTS'); ?></h3>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectosform'); ?>">
					<i class="icon-thumbs-up icon-large"></i><br /><br /><?php echo JText::_('LBL_PROJECT_NEW'); ?>
				</a>
			</div>
			<div class="span4">
				<a class="button btn btn-large btn-primary span9" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectoslist'); ?>" <?php if ($this->alta->project->count == 0) : echo 'disabled'; endif; ?>>
					<i class="icon-list icon-large"></i><br /><br /><?php echo JText::_('LBL_PROJECT_LIST'); ?>
				</a>
			</div>
		</div>
	</div>
</div>
