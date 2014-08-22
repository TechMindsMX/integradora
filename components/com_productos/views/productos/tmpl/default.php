<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$productos = $this->data;

?>
<h1><?php echo JText::_('COM_PRODUCTOS_LBL_TITULO'); ?></h1>

<?php
foreach ($productos as $key => $value) {
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_NAME').':</span> '.$value->name.'</div>';
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_DESCRIPTION').':</span> '.$value->description.'</div>';
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_MEDIDAS').':</span> '.$value->medida.'</div>';
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_PRECIO').':</span> '.$value->precio.'</div>';
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_IVA').':</span> '.$value->iva.'</div>';
	echo '<div class="producto"><span class="etiqueta">'.JText::_('COM_PRODUCTOS_LBL_IEPS').':</span> '.$value->ieps.'</div>';
	echo '<br />';
} 
?>