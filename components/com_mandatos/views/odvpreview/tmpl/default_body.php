<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

// Datos
$params 	= $app->input->getArray();

$integrado 	= $this->integCurrent->integrados[0];

$number2word = new AifLibNumber();

       // $isModal = $app->input->get('print') == 1; // 'print=1' will only be present in the url of the modal window, not in the presentation of the page
        // if( $isModal) {
                // $href = '"#" onclick="window.print(); return false;"';
        // } else {
                // $href = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
                // $href = "window.open(this.href,'win2','".$href."'); return false;";
                // $href = '"index.php?option=com_mandatos&view=odvpreview&integradoId='.$params['integradoId'].'&odvnum='.$params['odvnum'].'&tmpl=component&print=1" '.$href;
        // }
?>
<!--        <a href=--><?php //echo $href; ?><!-- >Click for Printing</a>-->

<?php //var_dump($this->odv); ?>

<div id="odv_preview">
<div class="clearfix" id="logo">
	<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
	<h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $this->odv->id; ?></h3>
</div>

<h1><?php echo JText::_('LBL_ORDEN_DE_VENTA'); ?></h1>

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
			<?php echo $this->odv->created; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PROY'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->proyecto->name; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->payment; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_SUBPROY'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->odv->sub_proyecto->name)) { echo $this->odv->sub_proyecto->name; } ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_FORMA_PAGO'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->paymentType; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_MONEDA'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->currency; ?>
		</div>
	</div>
</div>
<div class="clearfix" id="cuerpo">
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_RAZON_SOCIAL'); ?>
		</div>
		<div class="span10">
			<?php echo $this->odv->proveedor->tradeName; ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_RFC'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->proveedor->rfc; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_BANCOS'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->odv->banco)) { echo $this->odv->banco; } ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->proveedor->contact; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->odv->cuenta)) { echo $this->odv->cuenta; } ?>
		</div>
	</div>
	<div>
		<div class="span2 text-right">
			<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
		</div>
		<div class="span4">
			<?php echo $this->odv->proveedor->phone; ?>
		</div>
		<div class="span2 text-right">
			<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
		</div>
		<div class="span4">
			<?php if (isset($this->odv->clabe)) { echo $this->odv->clabe; } ?>
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
		foreach ($this->odv->productos as $key => $prod) :
			?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $prod['cantidad']; ?></td>
				<td><?php echo $prod['descripcion']; ?></td>
				<td><?php echo $prod['unidad']; ?></td>
				<td><div class="text-right">
						<?php echo number_format($prod['pUnitario'],2); ?>
					</div></td>
				<td><div class="text-right">
						<?php echo number_format(floatval($prod['cantidad']) * floatval($prod['pUnitario']),2); ?>
					</div></td>
			</tr>
		<?php
		endforeach;
		?>
		<tr>
			<td colspan="4" rowspan="3">
				<?php echo JText::_('LBL_MONTO_LETRAS'); ?> <span><?php echo $number2word->toCurrency('$'.number_format($this->odv->totalAmount + ($this->odv->totalAmount * $this->odv->iva), 2)); ?></span>
			</td>
			<td class="span2">
				<?php echo JText::_('LBL_SUBTOTAL'); ?>
			</td>
			<td><div class="text-right">
					<?php echo number_format($this->odv->totalAmount,2); ?>
				</div></td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo ($this->odv->iva * 100).'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
			</td>
			<td><div class="text-right">
					<?php echo number_format($this->odv->totalAmount * $this->odv->iva, 2); ?>
				</div></td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo JText::_('LBL_TOTAL'); ?>
			</td>
			<td><div class="text-right">
					<?php echo number_format($this->odv->totalAmount + ($this->odv->totalAmount * $this->odv->iva), 2); ?>
				</div></td>
		</tr>
		</tbody>
	</table>
	<div class="control-group" id="tabla-bottom">
		<div>
			<?php echo JText::_('LBL_OBSERVACIONES'); ?>
		</div>
		<div>
			<?php echo $this->odv->observaciones; ?>
		</div>
	</div>
	<div id="footer">
		<div class="container">
			<div class="control-group">
				<?php echo JText::_('LBL_CON_FACTURA'); ?>
			</div>
			<div class="container text-uppercase control-group">
				<?php echo JText::_('LBL_AUTORIZO_ODV'); ?>
			</div>
		</div>
		<div class="text-center">
			<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
			<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
		</div>
	</div>
</div>
</div>