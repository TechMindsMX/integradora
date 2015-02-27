<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params = $app->input->getArray();

$number2word = new AifLibNumber();
?>
<div id="factura_preview">
<?php echo $this->printBtn; ?>
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base() . '../images/logo_iecce.png'; ?>"/></div>
		<h3 class="span2 text-right">No. Orden</h3>

		<h3 class="span2 bordes-box text-center"><?php echo $this->factura->id; ?></h3>
	</div>

	<h1><?php echo 'Factura'; ?></h1>

<div class="clearfix" id="cabecera">
	<div>
		<div class="span2 text-right">
			<?php echo 'Socio Integrado'; ?>
		</div>
		<div class="span4">
			<?php //echo $this->integCurrent->integrados[0]->datos_empresa->razon_social; ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'Fecha de elaboración'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->createdDate; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'Proyecto'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proyecto->name; ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'Fecha de pago'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->paymentDate; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'Sub-proyecto'; ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->sub_proyecto->name)) {
				echo $this->factura->sub_proyecto->name;
			} ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'Forma de pago'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->paymentMethod->name; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'Moneda'; ?>
		</div>
		<div class="span4">
			<?php //echo $this->factura->currency; ?>
		</div>
	</div>
</div>
<div class="clearfix" id="cuerpo">
	<h4><?php echo 'Datos del Cliente'; ?></h4>
	<div>
		<div class="span2 text-right">
			<?php echo 'Denominación o Razón social'; ?>
		</div>
		<div class="span10">
			<?php echo $this->factura->proveedor->tradeName; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'RFC'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->rfc; ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'Banco'; ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->banco)) {
				echo $this->factura->banco;
			} ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'Contacto'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->contact; ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'Número de cuenta'; ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->cuenta)) {
				echo $this->factura->cuenta;
			} ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo 'Teléfono'; ?>
		</div>
		<div class="span4">
			<?php echo $this->factura->proveedor->phone; ?>
		</div>
		<div class="span2 text-right">
			<?php echo 'CLABE'; ?>
		</div>
		<div class="span4">
			<?php if (isset($this->factura->clabe)) {
				echo $this->factura->clabe;
			} ?>
		</div>
	</div>
	<div class="clearfix">
		<div class="span2 text-right">
			<?php echo 'Correo electrónico'; ?>
		</div>
		<div class="span4">

		</div>
	</div>
	<h3><?php echo 'Descripción de los servicio y/o productos'; ?></h3>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="span1">#</th>
			<th class="span2"><?php echo 'Cantidad'; ?></th>
			<th class="span4"><?php echo 'Descripción'; ?></th>
			<th class="span1"><?php echo 'Unidad'; ?></th>
			<th class="span2"><?php echo 'Precio Unitario'; ?></th>
			<th class="span2"><?php echo 'Importe'; ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->factura->productosData as $key => $prod) :
			$iva = $prod->iva;
            ?>
			<tr>
				<td><?php  echo $key; ?></td>
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
				<?php echo 'Cantidad en letra:'; ?>
				<span><?php echo $number2word->toCurrency('$' . number_format($this->factura->totalAmount + $this->factura->iva, 2)); ?></span>
			</td>
			<td class="span2">
				<?php echo 'SubTotal'; ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->totalAmount, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo ($iva ) . '% ' . 'IVA'; ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->iva, 2); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo 'Monto Total'; ?>
			</td>
			<td>
				<div class="text-right">
					<?php echo number_format($this->factura->totalAmount +  $this->factura->iva, 2); ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<div class="control-group" id="tabla-bottom">
		<div>
			<?php echo 'Observaciones'; ?>
		</div>
		<div>
			<?php // echo $this->factura->observaciones; ?>
		</div>
	</div>
	<div id="footer">
		<div class="container">
			<div class="control-group">
				<?php echo 'La presente Orden de Compra deberá de venir acompañada de la Factura por parte del proveedor a nombre de Integradora de Emprendimientos Culturales, SA de CV RFC: IEC 121203 FV8'; ?>
			</div>
			<div class="container text-uppercase control-group">
				<?php echo 'autorizo expresamente a integradora de emprendimientos culturales, s.a. de c.v.,conforme a los estatutos, politicas, reglas y procedimientos, a efectuar la factura y a recibir el pago a nombre y cuenta de mi representada'; ?>
			</div>
		</div>
		<div class="text-center">
			<p class="text-capitalize"><?php echo 'INTEGRADORA DE EMPRENDIMIENTOS CULTURALES, S.A. de C.V.'; ?></p>

			<p><?php echo 'Tiburcio Montiel 803, interior B3, San Miguel Chapultepec, Miguel Hidalgo, México D.F., 11850'; ?></p>
		</div>
	</div>
</div>
</div>

