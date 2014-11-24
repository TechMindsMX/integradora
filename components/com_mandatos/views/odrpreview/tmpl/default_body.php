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
                // $href = '"index.php?option=com_mandatos&view=odrpreview&integradoId='.$params['integradoId'].'&odrnum='.$params['odrnum'].'&tmpl=component&print=1" '.$href;
        // }
?>
<!--        <a href=--><?php //echo $href; ?><!-- >Click for Printing</a>-->


<div id="odr_preview">
	<div class="clearfix" id="logo">
		<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
		<h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $this->odr->idOdv; ?></h3>
	</div>	
	
	<h1><?php echo JText::_('LBL_ORDEN_DE_RETIRO'); ?></h1>
	
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
				<?php echo $this->odr->created; ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_MONEDA'); ?>
			</div>
			<div class="span4">
				<?php echo $this->odr->currency; ?>
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odr->payment)) {echo $this->odr->payment;} ?>
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
				<?php echo $this->odr->paymentType; ?>
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
				<?php echo JText::_('LBL_BANCOS'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta; } ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
			</div>
			<div class="span4">
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_BANCO_CUENTA'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta; } ?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
			</div>
			<div class="span4">
			</div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
			</div>
			<div class="span4">
				<?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta; } ?>
			</div>
		</div>
	</div>
	<div class="clearfix" id="cuerpo">
		<h3><?php echo JText::_('LBL_DESCRIP_IMPORTE_RETIRAR'); ?></h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="span7"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
					<th class="span5"><?php echo JText::_('LBL_MONTO_LETRAS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo '$ '.number_format($this->odr->totalAmount,2). ' ' . $this->odr->currency; ?></td>
					<td><?php echo $number2word->toCurrency('$'.number_format($this->odr->totalAmount,2)); ?></td>
				</tr>
			</tbody>
		</table>
		<div class="control-group" id="tabla-bottom">
			<div>
				<?php echo JText::_('LBL_OBSERVACIONES'); ?>
			</div>
			<div>
				<?php echo $this->odr->observaciones; ?>
			</div>
		</div>
		<div id="footer">
			<div class="container">
				<div class="control-group">
					<?php echo JText::_('LBL_DATOS_RETIRO'); ?>
				</div>
				<div class="container text-uppercase control-group">
					<?php echo JText::_('LBL_AUTORIZO_ODR'); ?>
				</div>
			</div>
			<div class="text-center">
				<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
				<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
			</div>
		</div>

    </div>
</div>