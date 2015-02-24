<?php
defined ('_JEXEC') or die('Restricted Access');

JHtml::_ ('bootstrap.tooltip');

$items = $this->items;

?>

<a class="btn btn-large btn-primary" href="<?php echo JRoute::_('index.php?option=com_adminintegradora&view=comisions'); ?>">
	<?php echo JText::_('COM_ADMININTEGRADORA_ADMIN_COMISIONES'); ?>
</a>


<?php
$vars['MEDIA_FILES'] = defined('MEDIA_FILES');
$vars['MIDDLE'] = defined('MIDDLE');
$vars['PUERTO'] = defined('PUERTO');
$vars['TIMONE'] = defined('TIMONE');
$vars['TIMONE_ROUTE'] = defined('TIMONE_ROUTE');
$vars['FACTURA_ROUTE'] = defined('FACTURA_ROUTE');
$vars['SEPOMEX_SERVICE'] = defined('SEPOMEX_SERVICE');

foreach ( $vars as $key => $constant ) {
	echo '<p>'.$key .' = '. $constant.'</p>';
}
