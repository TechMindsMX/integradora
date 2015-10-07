<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$rutas = array (
    array (
        array (
            'label'       => 'LBL_ODC',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=odclist')
        ),
        array (
            'label'       => 'LBL_ODV',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=odvlist')
        ),
//        array (
//            'label'       => 'COM_MANDATOS_GO_LIQUIDACION',
//            'buttonClass' => 'btn-primary',
//            'iconClass'   => 'icon-dollar',
//            'url'         => JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion')
//        ),
        array (
            'label'       => 'COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-stackexchange',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist')
        )
    ),
    array (
        array (
            'label'       => 'LBL_ODR',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=odrlist')
        ),
        array (
            'label'       => 'LBL_ODD',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=oddlist')
        ),
    ),
    array (
        array (
            'label'       => 'LBL_MUTUOS',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=mutuoslist')
        ),
        array (
            'buttonClass' => 'hide',
        ),
        array (
            'label'       => 'COM_MANDATOS_FACTURAS_X_PAGAR',
            'buttonClass' => 'btn-primary',
            'iconClass'   => 'icon-list-ul',
            'url'         => JRoute::_('index.php?option=com_mandatos&view=facturalist')
        )
    ),
);
$perRow = 3;
$rutas = array_chunk($rutas, $perRow, true);

?>

<div class="container">
	<h2><?php echo JText::_('COM_MANDATOS_TITULO_AUTH'); ?></h2>

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
</div>
