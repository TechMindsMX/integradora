<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

// Datos
$params 	    = $app->input->getArray();
$integrado 	    = $this->integCurrent->integrados[0];
$number2string  = new AifLibNumber();

$integ = new IntegradoSimple($integrado->integrado->integradoId);
$document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
?>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>
<table class="table">
    <tr>
        <td>
            <div><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
        </td>
        <td style="text-align: right">
            <h3 class="text-right">No. <?php echo $this->odd->numOrden; ?></h3>
        </td>
    </tr>
</table>
<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_ORDEN_DE_DEPOSITO'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_SOCIO_INTEG'); ?></td>
        <td style="text-align: left;"><?php echo $this->odd->receptor; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_DATE_CREATED'); ?></td>
        <td style="text-align: left;"><?php echo $this->odd->createdDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_MONEDA'); ?></td>
        <td style="text-align: left;"><?php echo $this->odd->currency = isset($this->odd->currency)?$this->odd->currency:'MXN'; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></td>
        <td style="text-align: left;"><?php if (isset($this->odd->paymentDate)) {echo $this->odd->paymentDate;} ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?></td>
        <td style="text-align: left;"><?php echo $integ->getDisplayName(); ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_FORMA_PAGO'); ?></td>
        <td style="text-align: left;"><?php echo JText::_($this->odd->paymentMethod->name); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?></td>
        <td style="text-align: left;"><?php echo $integrado->datos_personales->tel_fijo; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_CORREO'); ?></td>
        <td style="text-align: left;"><?php echo $integrado->datos_personales->email; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></td>
        <td style="text-align: left;"><?php if (isset($integrado->datos_bancarios[0]->banco_cuenta)) { echo 'XXXXXX' . substr($integrado->datos_bancarios[0]->banco_cuenta, -4, 4); } ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
</table>
<div class="clearfix"><h6><?php echo JText::_('LBL_DESCRIP_IMPORTE_DEPOSITAR'); ?></h6></div>
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
<table class="table" id="printFooter">
    <tr>
        <td><?php echo JText::_('LBL_OBSERVACIONES').'<br />'.JText::_('LBL_DATOS_DEPOSITO'); ?></td>
    </tr>
    <tr>
        <td><?php echo JText::_('LBL_AUTORIZO_FACTURA'); ?></td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
            <p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
        </td>
    </tr>
</table>