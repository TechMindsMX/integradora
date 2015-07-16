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
            <h3 class="text-right">No. <?php echo $this->odr->numOrden; ?></h3>
        </td>
    </tr>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_ORDEN_DE_RETIRO'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo JText::_('LBL_SOCIO_INTEG'); ?></td>
        <td style="text-align: left;"><?php echo $this->integCurrent->getDisplayName(); ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_DATE_CREATED'); ?></td>
        <td style="text-align: left; width: 20%;"><?php echo $this->odr->createdDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_MONEDA'); ?></td>
        <td style="text-align: left;"><?php echo $this->odr->currency; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></td>
        <td style="text-align: left;"><?php echo isset($this->odr->paymentDate) ? $this->odr->paymentDate : ''; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?></td>
        <td style="text-align: left;"><?php echo $integrado->datos_personales->nombre_representante; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_FORMA_PAGO'); ?></td>
        <td style="text-align: left;"><?php echo JText::_($this->odr->paymentMethod->name); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?></td>
        <td style="text-align: left;"><?php echo $this->integCurrent->getIntegradoPhone(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_CORREO'); ?></td>
        <td style="text-align: left;"><?php echo $integrado->datos_personales->email; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCOS'); ?></td>
        <td style="text-align: left;"><?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta->bankName; } ?></td>
    </tr>
    <tr>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></td>
        <td style="text-align: left;"><?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta->banco_cuenta; } ?></td>
    </tr>
    <tr>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
        <td style="text-align: right;"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></td>
        <td style="text-align: left;"><?php if (isset($this->odr->cuenta)) { echo $this->odr->cuenta->banco_clabe; } ?></td>
    </tr>
    <tr>
        <td colspan="5"><h6><?php echo JText::_('LBL_DESCRIP_IMPORTE_RETIRAR'); ?></h6></td>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span7"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
        <th class="span5"><?php echo JText::_('LBL_MONTO_LETRAS'); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php echo '$ '.number_format($this->odr->getTotalAmount(),2). ' ' . $this->odr->currency; ?></td>
        <td><?php echo $number2word->toCurrency('$'.number_format($this->odr->getTotalAmount(),2)); ?></td>
    </tr>
    </tbody>
</table>
<table class="table" id="printFooter">
    <tr>
        <td>
            <?php echo JText::_('LBL_DATOS_RETIRO'); ?>
        </td>
    </tr>
    <tr>
        <td><?php echo JText::_('LBL_AUTORIZO_ODR'); ?>
    </tr>
    <tr>
        <td style="text-align: center;">
            <p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
            <p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
        </td>
    </tr>
</table>