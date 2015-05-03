<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$integrados = $this->data;

$rutas = array(
	'COM_MANDATOS_LISTAD_PROYECTOS'             => JRoute::_('index.php?option=com_mandatos&view=proyectoslist'),
	'COM_MANDATOS_LISTAD_PRODUCTOS'             => JRoute::_('index.php?option=com_mandatos&view=productoslist'),
	'COM_MANDATOS_LISTAD_CLIENTES'              => JRoute::_('index.php?option=com_mandatos&view=clienteslist'),
	'COM_MANDATOS_LISTAD_ORDENES'               => JRoute::_('index.php?option=com_mandatos&view=odclist'),
	'COM_MANDATOS_LISTAD_ORDENES_DEPOSITO'      => JRoute::_('index.php?option=com_mandatos&view=oddlist'),
	'COM_MANDATOS_ORDENES_RETIRO_LISTADO'       => JRoute::_('index.php?option=com_mandatos&view=odrlist'),
	'COM_MANDATOS_ODV_LIST'                     => JRoute::_('index.php?option=com_mandatos&view=odvlist'),
	'COM_MANDATOS_FACTURA_LIST'                 => JRoute::_('index.php?option=com_mandatos&view=facturalist'),
	'COM_MANDATOS_MUTUOS'                       => JRoute::_('index.php?option=com_mandatos&view=mutuoslist'),
	'COM_MANDATOS_GO_LIQUIDACION'               => JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion'),
	'COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE'    => JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist')
);
$perRow = 1;
$rutas = array_chunk($rutas, $perRow, true);

echo '<h1>'.JText::_('COM_MANDATOS_TITULO').'</h1>';
?>

<div class="col-xs-12 col-md-6 col-lg-6">
	<div class="control-group form-horizontal row-fluid">
		<?php
		foreach ( $rutas as $fila ) {
		?>
			<div class="control-group row-fluid">
				<?php
				foreach ( $fila as $texto => $href ) {
					?>
					<div class="span<?php echo 12/$perRow; ?>">
						<a class="btn btn-primary" id="list_proyectos" href="<?php echo $href; ?>"><?php echo JText::_( $texto ); ?></a>
					</div>
				<?php
				}
				?>
				</div>
		<?php
		}
		?>
