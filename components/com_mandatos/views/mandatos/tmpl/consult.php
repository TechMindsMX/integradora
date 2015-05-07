<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$rutas = array(
	array(
		array('LBL_ODC' ,'icon-list'  ,JRoute::_('index.php?option=com_mandatos&view=odclist') ),
		array('LBL_ODR' ,'icon-list'  ,JRoute::_('index.php?option=com_mandatos&view=odrlist') ),
	),
	array(
		array('LBL_ODV' ,'icon-list'  ,JRoute::_('index.php?option=com_mandatos&view=odvlist') ),
		array('LBL_ODD' ,'icon-list'  ,JRoute::_('index.php?option=com_mandatos&view=oddlist') ),
	),
	array(
		array('LBL_MUTUOS' ,'icon-list'  ,JRoute::_('index.php?option=com_mandatos&view=mutuoslist') ),
	),
	array(
		array('COM_MANDATOS_AUTH_LIQUIDACION' ,'icon-dollar'  ,JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion') ),
		array('COM_MANDATOS_AUTH_TX_SIN_MANDATO' ,'icon-stackexchange'  ,JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist') )
	),
);
$perRow = 2;
$rutas = array_chunk($rutas, $perRow, true);

?>
<div class="container">
	<h2><?php echo JText::_('COM_MANDATOS_TITULO_CONSULT'); ?></h2>

	<div class="row-fluid">
		<div class="span8">
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
								   href="<?php echo $item[2]; ?>"><i class="<?php echo $item[1]; ?> icon-large"></i><br /><br />
									<?php echo JText::_( $item[0].'_PLURAL' ),'<br />
								<small><small>', JText::_( $item[0].'_SUB' ); ?></small></small></a>
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
