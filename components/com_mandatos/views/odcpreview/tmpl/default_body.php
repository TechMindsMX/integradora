<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
$document	 = JFactory::getDocument();
$app 		 = JFactory::getApplication();
$sesion      = JFactory::getSession();
$number2word = new AifLibNumber;
$orden       = $this->odc;
$msg         = $sesion->get('msg',null,'odcCorrecta');
$sesion->clear('msg','odcCorrecta');
$app->enqueueMessage($msg,'MESSAGE');

?>

<div id="odc_preview">
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
		<h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $orden->numOrden; ?></h3>
	</div>	
	
	<h1><?php echo JText::_('LBL_ORDEN_DE_COMPRA'); ?></h1>
	
	<div class="clearfix" id="cabecera">
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_SOCIO_INTEG'); ?>
			</div>
			<div class="span4">
				<?php echo $orden->emisor; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_DATE_CREATED'); ?>
			</div>
			<div class="span4">
				<?php echo $orden->createdDate; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PROY'); ?>
			</div>
			<div class="span4">
				<?php echo $orden->proyecto->name; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
			</div>
			<div class="span4">
				<?php echo $orden->paymentDate; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_SUBPROY'); ?>
			</div>
			<div class="span4">
				<?php if (isset($orden->subproyecto->name)) { echo $orden->subproyecto->name; } ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_FORMA_PAGO'); ?>
			</div>
			<div class="span4">
				<?php echo JText::_($orden->paymentMethod->name); ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_MONEDA'); ?>
			</div>
			<div class="span4">
				<?php echo isset($orden->currency)?$orden->currency:'MXN'; ?>
			</div>
		</div>
	</div>
	<div class="clearfix" id="cuerpo">
		<div class="proveedor form-group">

			<div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_RAZON_SOCIAL'); ?>
				</div>
				<div class="span10">
					<?php echo $orden->proveedor->frontName; ?>
				</div>
			</div>
			<div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_RFC'); ?>
				</div>
				<div class="span4">
					<?php echo ($orden->proveedor->type == getFromTimOne::getPersJuridica('moral')) ? $orden->proveedor->rfc : $orden->proveedor->pRFC; ?>
				</div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_BANCOS'); ?>
				</div>
				<div class="span4">
					<?php if (isset($orden->dataBank)) { echo $orden->dataBank[0]->bankName; } ?>
				</div>
			</div>
			<div>
				<div class="span2 text-right">
					<?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?>
				</div>
				<div class="span4">
					<?php echo $orden->proveedor->contact; ?>
				</div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
				</div>
				<div class="span4">
					<?php if (isset($orden->dataBank)) { echo $orden->dataBank[0]->banco_cuenta; } ?>
				</div>
			</div>
			<div>
				<div class="span2 text-right">
					<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
				</div>
				<div class="span4">
					<?php echo $orden->proveedor->phone; ?>
				</div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
				</div>
				<div class="span4">
					<?php if (isset($orden->dataBank)) { echo $orden->dataBank[0]->banco_clabe; } ?>
				</div>
			</div>
			<div class="clearfix">
				<div class="span2 text-right">
					<?php echo JText::_('LBL_CORREO'); ?>
				</div>
				<div class="span4">

				</div>
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
					foreach ($orden->productos as $key => $prod) : 
				?>
						<tr>
							<td><?php echo $key+1; ?></td>
							<td><?php echo $prod['CANTIDAD']; ?></td>
							<td><?php echo $prod['DESCRIPCION']; ?></td>
							<td><?php echo $prod['UNIDAD']; ?></td>
							<td><div class="text-right">
                                    $<?php echo number_format($prod['VALORUNITARIO'],2); ?>
							</div></td>
							<td><div class="text-right">
                                    $<?php echo number_format($prod['IMPORTE'],2); ?>
							</div></td>
						</tr>
				<?php
					endforeach;
				?>
				<tr>
					<td colspan="4" rowspan="3">
						<?php echo JText::_('LBL_MONTO_LETRAS'); ?> <span><?php echo $number2word->toCurrency('$'.number_format($orden->totalAmount,2)); ?></span>
					</td>
					<td class="span2">
						<?php echo JText::_('LBL_SUBTOTAL'); ?>
					</td>
					<td><div class="text-right">
                            $<?php $subtotal = $orden->totalAmount-$orden->impuestos; echo number_format($subtotal, 2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo $orden->iva->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
					</td>
					<td><div class="text-right">
                            $<?php echo number_format($orden->impuestos, 2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo JText::_('LBL_TOTAL'); ?>
					</td>
					<td><div class="text-right">
						$<?php echo number_format($orden->totalAmount, 2); ?>
					</div></td>
				</tr>
			</tbody>
		</table>
		<div class="control-group" id="tabla-bottom">
			<div>
				<?php echo JText::_('LBL_OBSERVACIONES'); ?>
			</div>
			<div>
				<?php echo $orden->observaciones; ?>
			</div>
		</div>
		<div id="footer">
			<div class="container">
				<div class="control-group">
					<?php echo JText::_('LBL_CON_FACTURA'); ?>
				</div>
				<div class="container text-uppercase control-group">
					<?php echo JText::_('LBL_AUTORIZO_ODC'); ?>
				</div>
			</div>
			<div class="text-center">
				<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
				<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
			</div>
		</div>
    </div>
</div>