<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$datos = $this->data;
$bancos = $this->catalogos->bancos;
$bankName = '';

foreach ($bancos as $value) {
    if($value->claveClabe == $datos->banco_codigo){
        $bankName = $value->banco;
    }
}
?>
<script>
    function ajax(parametros){

        var request = jQuery.ajax({
            url: parametros.link,
            data: parametros.datos,
            type: 'post'
        });

        return request;
    }

    function cancel() {
        var form = jQuery('#confirmForm');
        form.prop('action', 'index.php?option=com_mandatos&view=mutuosform');
        form.submit();
    }

    function confirm(){
        var form        = jQuery('#confirmForm');
        var data        = form.serialize();
        var url         = 'index.php?option=com_mandatos&task=mutuosform.saveMutuo&format=raw'
        var parametros  = {'datos': data, 'link':url};
        var request     = ajax(parametros);

        request.done(function(result){
            console.log(result);
            if(result.success && result.redirect){
                window.location = result.urlRedirect
            }
        });
    }

    jQuery(document).ready(function(){
        jQuery('#cancel').on('click',cancel);
        jQuery('#confirm').on('click',confirm);
    });
</script>

<h2><?php echo JText::_($this->titulo); ?></h2>

<p>Esta seguro de crear Mutuo con los siguientes Datos.</p>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?>: </div>
<div><strong><?php echo $datos->rfc; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO'); ?>: </div>
<div><strong><?php echo $datos->beneficiario; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_QUANTITYPAYMENTS'); ?>: </div>
<div><strong><?php echo $datos->quantityPayments; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTPERIOD'); ?>: </div>
<div><strong><?php echo $this->tipoPago[$datos->paymentPeriod]; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?>: </div>
<div><strong>$<?php echo number_format($datos->totalAmount,2); ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?>: </div>
<div><strong><?php echo $datos->interes; ?>%</strong></div>
<div class="clearfix">&nbsp;</div>

<?php if($bankName !== ''){?>
    <div id="dataBanco">
        <h3>Cuenta para depositos de abono</h3>

        <div><?php echo JText::_('LBL_BANCOS'); ?></div>
        <div><strong><?php echo $bankName; ?></strong></div>
        <div class="clearfix">&nbsp;</div>

        <div><?php echo JText::_('LBL_BANCO_CUENTA'); ?></div>
        <div><strong><?php echo $datos->banco_cuenta; ?></strong></div>
        <div class="clearfix">&nbsp;</div>

        <div><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></div>
        <div><strong><?php echo $datos->banco_sucursal; ?></strong></div>
        <div class="clearfix">&nbsp;</div>

        <div><?php echo JText::_('LBL_NUMERO_CLABE'); ?></div>
        <div><strong><?php echo $datos->banco_clabe; ?></strong></div>
        <div class="clearfix">&nbsp;</div>
    </div>
<?php } ?>

<form id="confirmForm" method="post" action="" autocomplete="off">
    <input type="hidden" id="idMutuo"          name="idMutuo"          value="<?php echo $datos->idMutuo; ?>" />
    <input type="hidden" id="integradoId"      name="integradoId"      value="<?php echo $datos->integradoId; ?>" />
    <input type="hidden" id="integradoIdR"     name="integradoIdR"     value="<?php echo $datos->integradoIdR; ?>" />
    <input type="hidden" id="rfc"              name="rfc"              value="<?php echo $datos->rfc; ?>" />
    <input type="hidden" id="integradoIdR"     name="integradoIdR"     value="<?php echo $datos->beneficiario; ?>" />
    <input type="hidden" id="paymentPeriod"    name="paymentPeriod"    value="<?php echo $datos->paymentPeriod; ?>" />
    <input type="hidden" id="quantityPayments" name="quantityPayments" value="<?php echo $datos->quantityPayments; ?>" />
    <input type="hidden" id="totalAmount"      name="totalAmount"      value="<?php echo $datos->totalAmount; ?>" />
    <input type="hidden" id="interes"          name="interes"          value="<?php echo $datos->interes; ?>" />
    <input type="hidden" id="jsonTabla"        name="jsonTabla"        value='<?php echo $datos->jsonTabla; ?>' />
    <input type="hidden" id="cuotaOcapital"    name="cuotaOcapital"    value='<?php echo $datos->cuotaOcapital; ?>' />

    <input type="hidden" id="banco_codigo"     name="banco_codigo"     value="<?php echo $datos->banco_codigo; ?>" />
    <input type="hidden" id="banco_cuenta"     name="banco_cuenta"     value="<?php echo $datos->banco_cuenta; ?>" />
    <input type="hidden" id="banco_sucursal"   name="banco_sucursal"   value="<?php echo $datos->banco_sucursal; ?>" />
    <input type="hidden" id="banco_clabe"      name="banco_clabe"      value="<?php echo $datos->banco_clabe; ?>" />


    <div class="row">
        <div class="span3">
            <input type="button" class="btn btn-primary" id="confirm" value="Confirmar" />
            <input type="button" class="btn btn-danger"  id="cancel" value="Cancelar" />
        </div>
    </div>
</form>