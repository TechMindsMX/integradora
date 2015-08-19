<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
require('libraries/html2pdf/html2pdf.class.php');
require('libraries/html2pdf/reportecontabilidad.php');

JHtml::_('behavior.keepalive');
$document	 = JFactory::getDocument();
$app 		 = JFactory::getApplication();
$sesion      = JFactory::getSession();
$number2word = new AifLibNumber;
$orden       = $this->odc;
$msg         = $sesion->get('msg',null,'odcCorrecta');
$sesion->clear('msg','odcCorrecta');
$app->enqueueMessage($msg,'MESSAGE');
ob_start();

$readcss = new reportecontabilidad();
$css = $readcss->readCss();
echo '<style>'.$css.'
}</style>';
?>
	<table style="width: 100%" id="logo">
       <tr>
           <td style="width: 569px;">
                <img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" />
           </td>
           <td style="width: 120px;">
               <h3 class=" text-right">No. Orden</h3>
           </td>
           <td >
               <h3 class=" bordes-box text-center"><?php echo $orden->numOrden; ?></h3>
           </td>
       </tr>
	</table>
	
	<h1><?php echo JText::_('LBL_ORDEN_DE_COMPRA'); ?></h1>
	
	<table class="clearfix" id="cabecera">
		<tr>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_SOCIO_INTEG'); ?>
			</td>
			<td class="span4">
				<?php echo $orden->emisor; ?>
			</td>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_DATE_CREATED'); ?>
			</td>
			<td class="span4">
				<?php echo $orden->createdDate; ?>
			</td>
		</tr>
		<tr>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_PROY'); ?>
			</td>
			<td class="span4">
				<?php echo isset($orden->proyecto->name) ? $orden->proyecto->name : ''; ?>
			</td>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>
			</td>
			<td class="span4">
				<?php echo $orden->paymentDate; ?>
			</td>
		</tr>
		<tr>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_SUBPROY'); ?>
			</td>
			<td class="span4">
				<?php if (isset($orden->subproyecto->name)) { echo $orden->subproyecto->name; } ?>
			</td>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_FORMA_PAGO'); ?>
			</td>
			<td class="span4">
				<?php echo JText::_($orden->paymentMethod->name); ?>
			</td>
		</tr>
		<tr>
			<td class="span2 text-right">
				<?php echo JText::_('LBL_MONEDA'); ?>
			</td>
			<td class="span4">
				<?php echo isset($orden->currency)?$orden->currency:'MXN'; ?>
			</td>
		</tr>
	</table>
	<div class="clearfix" id="cuerpo">
		<div class="proveedor form-group">

			<div>
				<div class="span2 text-right" style="width: 100px">
					<?php echo JText::_('LBL_RAZON_SOCIAL'); ?>
				</div>
				<div class="span10" style="150px">
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
					<?php
                    if (isset($orden->dataBank)) {
                        echo isset($orden->dataBank[0]->bankName) ? $orden->dataBank[0]->bankName: 'STP';
                    }
                    ?>
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
					<?php
                    if (isset($orden->dataBank)) {
                        $banco = !isset($orden->dataBank[0]->banco_cuenta) ? 'Cuenta STP' : $orden->dataBank[0]->banco_cuenta;
                        echo $banco;
                    }
                    ?>
				</div>
			</div>
			<div>
				<div class="span2 text-right">
					<?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>
				</div>
				<div class="span4">
					<?php echo $orden->receptor->getIntegradoPhone(); ?>
				</div>
				<div class="span2 text-right">
					<?php echo JText::_('LBL_NUMERO_CLABE'); ?>
				</div>
				<div class="span4">
					<?php echo !empty($orden->dataBank) ? $orden->dataBank[0]->banco_clabe : ''; ?>
				</div>
			</div>
			<div class="clearfix">
				<div class="span2 text-right">
					<?php echo JText::_('LBL_CORREO'); ?>
				</div>
				<div class="span4">
					<?php echo $orden->receptor->getIntegradoEmail(); ?>
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
					<td colspan="4" rowspan="4">
						<?php echo JText::_('LBL_MONTO_LETRAS'); ?> <span><?php echo $number2word->toCurrency('$'.number_format($orden->totalAmount,2)); ?></span>
					</td>
					<td class="span2">
						<?php echo JText::_('LBL_SUBTOTAL'); ?>
					</td>
					<td><div class="text-right">
                            $<?php $subtotal = $orden->totalAmount - $orden->impuestos; echo number_format($subtotal, 2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo $orden->iva->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
					</td>
					<td><div class="text-right">
                            $<?php echo number_format($orden->iva->importe, 2); ?>
					</div></td>
				</tr>
				<tr>
					<td class="span2">
						<?php echo $orden->ieps->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>
					</td>
					<td><div class="text-right">
                            $<?php echo number_format($orden->ieps->importe, 2); ?>
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
<?php

$html2 = ob_get_clean();
echo $html2;
$html2pdf = new HTML2PDF();
$html2pdf->WriteHTML($html2);
$html2pdf->Output('respaldosPDF/ODC-'.$orden->id.'.pdf', 'F');
/*
*/