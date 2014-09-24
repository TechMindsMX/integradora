<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<h1>'.JText::_('COM_MANDATOS_TITULO').'</h1>';
?>
<div style="margin-top: 50px;">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectoslist&integradoId='.$this->integradoId); ?>">
        <?php echo JText::_('COM_MANDATOS_LISTAD_PROYECTOS'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productoslist&integradoId='.$this->integradoId); ?>">
        <?php echo JText::_('COM_MANDATOS_LISTAD_PRODUCTOS'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clienteslist&integradoId='.$this->integradoId); ?>">
        <?php echo JText::_('COM_MANDATOS_LISTAD_CLIENTES'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odclist&integradoId='.$this->integradoId); ?>">
        <?php echo JText::_('COM_MANDATOS_LISTAD_ORDENES'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=oddlist&integradoId='.$this->integradoId); ?>">
        <?php echo JText::_('COM_MANDATOS_LISTAD_ORDENES_DEPOSITO'); ?>
    </a>
</div>

<div class="margen-fila">
	<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId='.$this->integradoId); ?>">
	<?php echo JText::_('COM_MANDATOS_LISTAD_ORDENES_VENTA'); ?>
	</a>
</div>
