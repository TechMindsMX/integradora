<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<h1>'.JText::_('COM_REPORTES_TITLE').'</h1>';
?>
<div style="margin-top: 50px;">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_reportes&view=reporteslistados&integradoId=1'); ?>">
        <?php echo JText::_('COM_REPORTES_LISTAD_REPORTES'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productoslist&integradoId='); ?>">
        <?php echo JText::_('COM_REPORTES_ESTADO_DE_FLUJO'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clienteslist&integradoId='); ?>">
        <?php echo JText::_('COM_REPORTES_ESTADO_DE_RESULTADOS'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odclist&integradoId='); ?>">
        <?php echo JText::_('COM_RESULTADOS_BALANCE'); ?>
    </a>
</div>

<div class="margen-fila">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=oddlist&integradoId='); ?>">
        <?php echo JText::_('COM_RESULTADOS_LISTADO_DE_REPORTES'); ?>
    </a>
</div>
