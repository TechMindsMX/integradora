<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

// Datos
$params 	= $app->input->getArray();

?>

<div id="odc_preview">
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
		<h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $this->odc->id; ?></h3>
	</div>	
	
	<h1><?php echo JText::_('LBL_ORDEN_DE_COMPRA'); ?></h1>
	
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
				<?php echo $this->odc->created; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PROY'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->proyecto->name; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->payment; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_SUBPROY'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odc->sub_proyecto->name)) { echo $this->odc->sub_proyecto->name; } ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_FORMA_PAGO'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->paymentType; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_MONEDA'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->currency; ?>
			</div>
		</div>
	</div>
	<div class="clearfix" id="cuerpo">
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_RAZON_SOCIAL'); ?>
			</div>
			<div class="span10">
				<?php echo $this->odc->proveedor->tradeName; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_RFC'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->proveedor->rfc; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_BANCOS'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odc->banco)) { echo $this->odc->banco; } ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->proveedor->contact; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odc->cuenta)) { echo $this->odc->cuenta; } ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odc->proveedor->phone; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odc->clabe)) { echo $this->odc->clabe; } ?>
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
					foreach ($this->odc->productos as $key => $prod) : 
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
						<?php echo JText::_('LBL_MONTO_LETRAS'); ?>
					</td>
					<td class="span2">
						<?php echo JText::_('LBL_SUBTOTAL'); ?>
					</td>
					<td><div class="text-right">
						<?php echo number_format($this->odc->amount,2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'  '.($this->odc->iva * 100).'%'; ?>
					</td>
					<td><div class="text-right">
						<?php echo number_format($this->odc->amount * $this->odc->iva, 2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo JText::_('LBL_TOTAL'); ?>
					</td>
					<td><div class="text-right">
						<?php echo number_format($this->odc->amount + ($this->odc->amount * $this->odc->iva), 2); ?>
					</div></td>
				</tr>
			</tbody>
		</table>
		<div class="control-group" id="tabla-bottom">
			<div>
				<?php echo JText::_('LBL_OBSERVACIONES'); ?>
			</div>
			<div>
				<?php echo $this->odc->observaciones; ?>
			</div>
		</div>
		<div id="footer">
			<div class="container">
				<div class="control-group">
					<?php echo JText::_('LBL_CON_FACTURA'); ?>
				</div>
				<div class="container text-uppercase control-group">
					<?php echo JText::_('LBL_AUTORIZO'); ?>
				</div>
				<div class="row">
					<div class="span4 text-center"><?php echo JText::_('LBL_FIRMA_1'); ?></div>
					<div class="span4 text-center"><?php echo JText::_('LBL_FIRMA_2'); ?></div>
					<div class="span4 text-center"><?php echo JText::_('LBL_FIRMA_3'); ?></div>
				</div>
			</div>
			<div class="text-center">
				<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
				<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
			</div>
		</div>

        <div class="container botones">
        <?php
        if($this->permisos['canAuth'] && $this->odc->status === 0 ):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odcpreview&task=authorize&integradoId='.$this->integradoId.'&odcnum='.$this->odc->id);
        ?>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_ÄUTORIZE'); ?></a>
        <?php
        endif;
        ?>
            <a class="btn btn-danger" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odclist&integradoId='.$this->integradoId); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </div>
    </div>
</div>