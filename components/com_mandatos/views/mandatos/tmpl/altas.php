<?php
defined('_JEXEC') or die('Restricted access');

$buttonClass = "button btn btn-large btn-primary span9";
$iconClass = "icon-3x";

$cliprovList = ($this->alta->cliprov->count == 0) ? ['href' => '#', 'disabled' => 'disabled="disabled"'] : ['href' => JRoute::_('index.php?option=com_mandatos&view=clienteslist'), 'disabled' => ''];
$productList = ($this->alta->product->count == 0) ? ['href' => '#', 'disabled' => 'disabled="disabled"'] : ['href' => JRoute::_('index.php?option=com_mandatos&view=productoslist'), 'disabled' => ''];
$projectList = ($this->alta->project->count == 0) ? ['href' => '#', 'disabled' => 'disabled="disabled"'] : ['href' => JRoute::_('index.php?option=com_mandatos&view=proyectoslist'), 'disabled' => ''];
?>

<div class="container">
	<div class="">
		<h2 class="item-title"><?php echo JText::_('LBL_ALTAS'); ?></h2>

        <div class="row-fluid">
            <div class="span4">
                <a class="<?php echo $buttonClass; ?>" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clientesform'); ?>" >
                    <i class="icon-thumbs-up <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_CLIENTES_PROVEEDORES_NEW'); ?>
                </a>
            </div>
            <div class="span4">
                <a class="<?php echo $buttonClass; ?>" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productosform'); ?>" >
                    <i class="icon-thumbs-up <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_PRODUCT_NEW'); ?>
                </a>
            </div>
            <div class="span4">
                <a class="<?php echo $buttonClass; ?>" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectosform'); ?>">
                    <i class="icon-thumbs-up <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_PROJECT_NEW'); ?>
                </a>
            </div>
        </div>

        <br class="clearfix">
        <h2 class="item-title"><?php echo JText::_('LBL_LISTADOS'); ?></h2>
        <div class="row-fluid">
            <div class="span4">
                <a class="<?php echo $buttonClass; ?>" href="<?php echo $cliprovList['href']; ?>" <?php echo $cliprovList['disabled']; ?>>
                    <i class="icon-list-ul <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_CLIENTES_PROVEEDORES_LIST'); ?>
                </a>
            </div>
            <div class="span4">
                <a class="<?php echo $buttonClass; ?>" href="<?php echo $productList['href']; ?>" <?php echo $productList['disabled']; ?>>
                    <i class="icon-list-ul <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_PRODUCT_LIST'); ?>
                </a>
            </div>
			<div class="span4">
				<a class="<?php echo $buttonClass; ?>" href="<?php echo $projectList['href']; ?>" <?php echo $projectList['disabled'] ?>>
					<i class="icon-list-ul <?php echo $iconClass; ?>"></i><br /><br /><?php echo JText::_('LBL_PROJECT_LIST'); ?>
				</a>
			</div>
		</div>
	</div>
</div>
