<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<h1>'.JText::_('COM_MANDATOS_TITULO').'</h1>';
?>
<div style="margin-top: 50px;"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectos&integradoId='.$this->integradoId); ?>" /><?php echo JText::_('COM_MANDATOS_LISTAD_PROYECTOS'); ?></a></div>
<div style="margin-top: 20px;"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=productos&integradoId='.$this->integradoId); ?>" /><?php echo JText::_('COM_MANDATOS_LISTAD_PRODUCTOS'); ?></a></div>
<div style="margin-top: 20px;"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=clientes&integradoId='.$this->integradoId); ?>" /><?php echo JText::_('COM_MANDATOS_LISTAD_CLIENTES'); ?></a></div>
