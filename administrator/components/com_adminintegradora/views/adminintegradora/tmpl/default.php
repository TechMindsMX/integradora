<?php
defined ('_JEXEC') or die('Restricted Access');

JHtml::_ ('bootstrap.tooltip');

$items = $this->items;

?>

<div class="span2">
    <a class="btn btn-large btn-primary" href="<?php echo JRoute::_('index.php?option=com_adminintegradora&view=comisions'); ?>">
        <?php echo JText::_('COM_ADMININTEGRADORA_ADMIN_COMISIONES'); ?>
    </a>
</div>

<div class="span2">
    <a class="btn btn-large btn-primary" href="<?php echo JRoute::_('index.php?option=com_adminintegradora&view=oddlist'); ?>">
        <?php echo JText::_('COM_ADMININTEGRADORA_ADMIN_CONCILIACIONES'); ?>
    </a>
</div>