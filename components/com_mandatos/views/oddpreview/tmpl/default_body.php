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

$number2string = new AifLibNumber();

echo $this->printBtn;
?>

<div id="odd_preview">
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
		<h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $this->odd->numOrden; ?></h3>
	</div>	
	
	<h1><?php echo JText::_('LBL_ORDEN_DE_DEPOSITO'); ?></h1>
	
	<div class="clearfix" id="cabecera">
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_SOCIO_INTEG'); ?>
			</div>
			<div class="span4">
				<?php echo $integrado->datos_empresa->razon_social; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_DATE_CREATED'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odd->createdDate; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_MONEDA'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odd->currency = isset($this->odd->currency)?$this->odd->currency:'MXN'; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odd->paymentDate)) {echo $this->odd->paymentDate;} ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?>
			</div>
			<div class="span4">
				<?php echo $integrado->datos_empresa->razon_social; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_FORMA_PAGO'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odd->paymentMethod->name; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
			</div>
			<div class="span4">
				<?php echo $integrado->datos_empresa->tel_fijo; ?>
			</div>
			<div class="span2 text-right">
			</div>
			<div class="span4">
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_CORREO'); ?>
			</div>
			<div class="span4">
				<?php echo $integrado->datos_personales->email; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odd->cuenta)) { echo $this->odd->cuenta; } ?>
			</div>
		</div>
	</div>
	<div class="clearfix" id="cuerpo">
		<h3><?php echo JText::_('LBL_DESCRIP_IMPORTE_DEPOSITAR'); ?></h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="span7"></th>
					<th class="span5"><?php echo JText::_('LBL_MONTO_LETRAS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo '$ '.number_format($this->odd->totalAmount,2). ' ' . $this->odd->currency; ?></td>
					<td><?php echo $number2string->toCurrency('$ '.number_format($this->odd->totalAmount,2)); ?></td>
				</tr>
			</tbody>
		</table>
		<div class="control-group" id="tabla-bottom">
			<div>
				<?php echo JText::_('LBL_OBSERVACIONES'); ?>
			</div>

		</div>
		<div id="footer">
			<div class="container">
				<div class="control-group">
					<?php echo JText::_('LBL_DATOS_DEPOSITO'); ?>
				</div>
				<div class="container text-uppercase control-group">
					<?php echo JText::_('LBL_AUTORIZO_ODD'); ?>
				</div>
			</div>
			<div class="text-center">
				<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
				<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
			</div>
		</div>

    </div>
</div>