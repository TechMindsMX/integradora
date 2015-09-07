<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$rutas = array(
	array(
		array('label'=>'COM_MANDATOS_ORDENES_LBL_AGREGAR', 'buttonClass'=>'btn-primary', 'iconClass'=>'icon-plus-sign', 'url'=>JRoute::_('index.php?option=com_mandatos&view=odcform') ),
		array('label'=>'COM_MANDATOS_ORDENES_RETIRO_AGREGAR', 'buttonClass'=>'btn-primary', 'iconClass'=>'icon-plus-sign', 'url'=>JRoute::_('index.php?option=com_mandatos&view=odrform') ),
		array('label'=>'COM_MANDATOS_GO_LIQUIDACION', 'buttonClass'=>'btn-warning', 'iconClass'=>'icon-dollar', 'url'=>JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion') ),
	),
	array(
		array('label'=>'COM_MANDATOS_ORV_LBL_AGREGAR', 'buttonClass'=>'btn-primary', 'iconClass'=>'icon-plus-sign', 'url'=>JRoute::_('index.php?option=com_mandatos&view=odvform') ),
		array('label'=>'COM_MANDATOS_ORDENES_DEPOSITO_LBL_AGREGAR', 'buttonClass'=>'btn-primary', 'iconClass'=>'icon-plus-sign', 'url'=>JRoute::_('index.php?option=com_mandatos&view=oddform') ),
		array('label'=>'COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE', 'buttonClass'=>'btn-warning', 'iconClass'=>'icon-stackexchange', 'url'=>JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist') )
	),
	array(
		array('label'=>'COM_MANDATOS_MUTUOS_FORM_TITULO', 'buttonClass'=>'btn-primary', 'iconClass'=>'icon-plus-sign','url'=>JRoute::_('index.php?option=com_mandatos&view=mutuosform') ),
	),
	array(
	),
);
$perRow = 3;
$rutas = array_chunk($rutas, $perRow, true);

?>

<div class="container">
	<h2><?php echo JText::_('COM_MANDATOS_TITULO'); ?></h2>

	<div class="row-fluid">
		<div class="span12">
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
									<a class="btn <?php echo $item['buttonClass']; ?> btn-large span9" id="list_proyectos"
									   href="<?php echo $item['url']; ?>"><i class="<?php echo $item['iconClass']; ?> icon-3x"></i><br /><br />
										<?php echo JText::_( $item['label'] ),'<br /><small><small>', JText::_( $item['label'].'_SUB' ); ?></small></small>
									</a>
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
	</div>
</div>
