<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params      = $app->input->getArray();
$integrado   = $this->integCurrent->integrados[0];
$integradora = new \Integralib\Integrado();
$integradora = new IntegradoSimple( $integradora->getIntegradoraUuid() );
$number2word = new AifLibNumber();

?>

<div class="hidden-print form-group">
	<?php echo $this->printBtn; ?>
</div>

<div id="factura_preview">

	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
		<h3 class="span2 text-right">No. </h3>

		<h3 class="span2 bordes-box text-center"><?php echo $this->factura->getId(); ?></h3>
	</div>

	<h1><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h1>

<div class="clearfix" id="cabecera">
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_SOCIO_INTEG'); ?>
		</div>
		<div class="span4">
			<?php echo $integradora->getDisplayName(); ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_DATE_CREATED'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->getCreatedDate(); ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PROY'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->project->name; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->paymentDate; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_SUBPROY'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->subProject->name)) {
				echo $this->factura->subProject->name;
			} ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_FORMA_PAGO'); ?>
		</div>
		<div class="span4">
			<?php echo JText::_($this->factura->paymentMethod->name); ?>
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
			<?php echo $this->factura->getReceptor()->getIntegradoRfc();; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_BANCOS'); ?>
		</div>
		<div class="span4">
			<?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->bankName; ?>
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
			<?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->banco_cuenta; ?>
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
			<?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->banco_clabe; ?>
		</div>
	</div>
	<div class="clearfix">
		<div class="span2 text-right">
			<?php echo JText::_('LBL_CORREO'); ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->getEmisor()->getIntegradoEmail(); ?>
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
		foreach ($this->factura->productosData as $key => $prod) :
			$ivasProd = array();
			array_push($ivasProd, floatval($prod->iva) );
			?>
			<tr>
				<td><?php echo $key+1; ?></td>
				<td><?php echo $prod->cantidad; ?></td>
				<td><?php echo $prod->descripcion; ?></td>
				<td><?php echo $prod->unidad; ?></td>
				<td>
					<div class="text-right">
						<?php echo number_format($prod->p_unitario, 2); ?>
					</div>
				</td>
				<td>
					<div class="text-right">
						<?php echo number_format(floatval($prod->cantidad) * floatval($prod->p_unitario), 2); ?>
					</div>
				</td>
			</tr>
		<?php
		endforeach;
		?>
		<tr>
			<td colspan="4" rowspan="3">
				<?php echo JText::_('LBL_MONTO_LETRAS'); ?>
				<span><?php echo $number2word->toCurrency('$' . number_format($this->factura->getTotalAmount() + ($this->factura->getTotalAmount() * $this->factura->iva), 2)); ?></span>
			</td>
			<td class="span2">
				<?php echo JText::_('LBL_SUBTOTAL'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->subTotalAmount, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo array_sum($ivasProd)/count($ivasProd) . '% ' . JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->iva, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo JText::_('LBL_TOTAL'); ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->getTotalAmount(), 2); ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<div id="footer">
		<div class="container">
			<div class="control-group">
				<?php echo JText::_('LBL_DATOS_DEPOSITO'); ?>
			</div>
			<div class="container text-uppercase control-group">
				<?php echo JText::_('LBL_AUTORIZO_ODV').$this->integCurrent->getDisplayName().' con RFC: '.$this->integCurrent->integrados[0]->datos_empresa->rfc; ?>
			</div>
		</div>
		<div class="text-center">
			<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>

			<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
		</div>
	</div>
</div>
</div>
