<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$integrados = $this->data;

$rutas = array(
	array(
		array('COM_MANDATOS_ORDENES_LBL_AGREGAR' ,'icon-plus-sign'  ,JRoute::_('index.php?option=com_mandatos&view=odcform') ),
		array('COM_MANDATOS_ORDENES_RETIRO_AGREGAR' ,'icon-plus-sign'  ,JRoute::_('index.php?option=com_mandatos&view=odrform') ),
	),
	array(
		array('COM_MANDATOS_ORV_LBL_AGREGAR' ,'icon-plus-sign'  ,JRoute::_('index.php?option=com_mandatos&view=odvform') ),
		array('COM_MANDATOS_ORDENES_DEPOSITO_LBL_AGREGAR' ,'icon-plus-sign'  ,JRoute::_('index.php?option=com_mandatos&view=oddform') ),
	),
	array(
		array('COM_MANDATOS_MUTUOS_FORM_TITULO' ,'icon-plus-sign'  ,JRoute::_('index.php?option=com_mandatos&view=mutuosform') ),
	),
	array(
		array('COM_MANDATOS_GO_LIQUIDACION' ,'icon-dollar'  ,JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion') ),
		array('COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE' ,'icon-stackexchange'  ,JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist') )
	),
);
$perRow = 2;
$rutas = array_chunk($rutas, $perRow, true);

echo '<h2>'.JText::_('COM_MANDATOS_TITULO').'</h2>';
?>

<div class="span8" xmlns="http://www.w3.org/1999/html">
	<div class="row-fluid">
		<?php
		foreach ( $rutas as $group ) {
		foreach ( $group as $fila ) {
			?>
			<div class="control-group row-fluid">
			<?php
			foreach ( $fila as $item ) {
				?>
				<div class="span<?php echo 12 / $perRow; ?>">
					<a class="btn btn-primary btn-large span11" id="list_proyectos"
					   href="<?php echo $item[2]; ?>"><i class="<?php echo $item[1]; ?> icon-large"></i><br /><br /><?php echo JText::_( $item[0] ),'<br /><small><small>', JText::_( $item[0].'_SUB' ); ?></small></small></a>
				</div>
			<?php
			}
			?>
			</div>
			<br>
		<?php
		}
		}
		?>
	</div>
</div>