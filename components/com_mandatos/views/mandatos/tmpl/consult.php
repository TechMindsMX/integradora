<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$rutas = array (
	array (
		array ('label'       => 'LBL_ODC',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=odclist')
		),
		array ('label'       => 'LBL_ODV',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=odvlist')
		),
		array ('label'       => 'COM_MANDATOS_AUTH_TX_SIN_MANDATO',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-stackexchange',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist')
		),
	),
	array (
		array ('label'       => 'LBL_ODR',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=odrlist')
		),
		array ('label'       => 'LBL_ODD',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=oddlist')
		),
		array ('label'       => 'COM_MANDATOS_AUTH_LIQUIDACION',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-dollar',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion')
		),
	),
	array (
		array ('label'       => 'LBL_MUTUOS',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=mutuoslist')
		),
		array ('buttonClass' => 'hide',
		),
		array ('label'       => 'COM_MANDATOS_FACTURAS',
		       'buttonClass' => 'btn-primary',
		       'iconClass'   => 'icon-list-ul',
		       'url'         => JRoute::_('index.php?option=com_mandatos&view=facturalist&layout=listFact')
		),

	),
);
$perRow = 3;
$rutas = array_chunk($rutas, $perRow, true);

?>
<div class="container">
	<h2><?php echo JText::_('COM_MANDATOS_TITULO_CONSULT'); ?></h2>

	<div class="row-fluid">
		<div class="span12">
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
									<?php echo JText::_( $item['label'].'_PLURAL' ),'<br />
								<small><small>', JText::_( $item['label'].'_SUB' ); ?></small></small></a>
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
