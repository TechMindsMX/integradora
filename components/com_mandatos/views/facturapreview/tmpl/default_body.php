<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params = $app->input->getArray();

$integrado = $this->integCurrent->integrados[0];

$number2word = new AifLibNumber();

?>

<div id="factura_preview">
<?php echo $this->printBtn; ?>
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
		<h3 class="span2 text-right">No. Orden</h3>

		<h3 class="span2 bordes-box text-center"><?php echo $this->factura->id; ?></h3>
	</div>

	<h1><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h1>

<div class="clearfix" id="cabecera">
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_SOCIO_INTEG'); ?>
		</div>
		<div class="span4">
			<?php echo $this->integCurrent->integrados[0]->datos_empresa->razon_social; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_DATE_CREATED'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->created; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PROY'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proyecto->name; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->payment; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_SUBPROY'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->sub_proyecto->name)) {
				echo $this->factura->sub_proyecto->name;
			} ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_FORMA_PAGO'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->paymentType; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_MONEDA'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->currency; ?>
		</div>
	</div>
</div>
<div class="clearfix" id="cuerpo">
	<h4><?php echo JText::_('LBL_HEADER_DATOS_CLIENTE'); ?></h4>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_RAZON_SOCIAL'); ?>
		</div>
		<div class="span10">
			<?php echo $this->factura->proveedor->tradeName; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_RFC'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->rfc; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_BANCOS'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->banco)) {
				echo $this->factura->banco;
			} ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->contact; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->cuenta)) {
				echo $this->factura->cuenta;
			} ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->phone; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->clabe)) {
				echo $this->factura->clabe;
			} ?>
		</div>
	</div>
	<div class="clearfix">
		<div class="span2 text-right">
			<?php echo JText::_('LBL_CORREO'); ?>
		</div>
		<div class="span4">

		</div>
	</div>
	<h3><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h3>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="span1">#</th>
			<th class="span2"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
			<th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
			<th class="span1"><?php echo JText::_('LBL_UNIDAD'); ?></th>
			<th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
			<th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->factura->productos as $key => $prod) :
			?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $prod['cantidad']; ?></td>
				<td><?php echo $prod['descripcion']; ?></td>
				<td><?php echo $prod['unidad']; ?></td>
				<td>
					<div class="text-right">
						<?php echo number_format($prod['pUnitario'], 2); ?>
					</div>
				</td>
				<td>
					<div class="text-right">
						<?php echo number_format(floatval($prod['cantidad']) * floatval($prod['pUnitario']), 2); ?>
					</div>
				</td>
			</tr>
		<?php
		endforeach;
		?>
		<tr>
			<td colspan="4" rowspan="3">
				<?php echo JText::_('LBL_MONTO_LETRAS'); ?>
				<span><?php echo $number2word->toCurrency('$' . number_format($this->factura->totalAmount + ($this->factura->totalAmount * $this->factura->iva), 2)); ?></span>
			</td>
			<td class="span2">
				<?php echo JText::_('LBL_SUBTOTAL'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->totalAmount, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo ($this->factura->iva * 100) . '% ' . JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->totalAmount * $this->factura->iva, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo JText::_('LBL_TOTAL'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->totalAmount + ($this->factura->totalAmount * $this->factura->iva), 2); ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<div class="control-group" id="tabla-bottom">
		<div>
			<?php echo JText::_('LBL_OBSERVACIONES'); ?>
		</div>
		<div>
			<?php echo $this->factura->observaciones; ?>
		</div>
	</div>
	<div id="footer">
		<div class="container">
			<div class="control-group">
				<?php echo JText::_('LBL_CON_FACTURA'); ?>
			</div>
			<div class="container text-uppercase control-group">
				<?php echo JText::_('LBL_AUTORIZO_FACTURA'); ?>
			</div>
		</div>
		<div class="text-center">
			<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>

			<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
		</div>
	</div>
</div>
</div>

