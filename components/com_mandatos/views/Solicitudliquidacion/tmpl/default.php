<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';

$operaciones = $this->operaciones;
$saldo = $this->saldo;

?>
<script>
    function validaMonto() {
        var campo = jQuery(this);
        var monto = campo.val();
        var saldo = jQuery('#saldoLiquidacion').val();

        if(parseFloat(monto) > parseFloat(saldo)){
            mensajes('<?php echo JText::_('MENSSAGE_ERROR_MONTO_VS_SALDO') ?>','error',campo.prop('id'));
            jQuery('#liquidar').prop('disabled', true);
        }else{
            jQuery('#liquidar').prop('disabled', false);
        }
    }
    function liquidar() {
        var data = jQuery('#form_solicitudLiquidacion').serialize();

        var request = jQuery.ajax({
            url: "index.php?option=com_mandatos&task=solicitudliquidacion.saveform&format=raw",
            data: data,
            type: 'post',
            async: false
        });

        request.done(function(result){
            console.log(result);
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }
    jQuery(document).ready(function(){
        jQuery('#monto').on('change', validaMonto);
        jQuery('#liquidar').on('click', liquidar);
    });
</script>
<h1 style="margin-bottom: 40px;"><?php echo JText::_('COM_MANDATOS_GO_LIQUIDACION'); ?></h1>
    <form id="form_solicitudLiquidacion">
        <div>
            <h4><?php echo JText::_('COM_MANDATOS_LIQUIDACION_SALDO').': $'.number_format($saldo,2); ?></h4>
            <input type="hidden" value=" <?php echo $saldo; ?>" id="saldoLiquidacion" class="saldoliquidacion" name="saldo" />
        </div>

        <div>
            <label for="monto"><?php echo JText::_('COM_MANDATOS_LBL_MONTO_SL'); ?></label>
            <input type="text" name="monto" id="monto" />
        </div>

        <div class="form-actions" style="max-width: 30%">
            <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
            <button type="button" class="btn btn-primary span3" id="liquidar"><?php echo JText::_('LBL_ENVIAR'); ?></button>
            <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
        </div>
    </form>

<h3><?php echo JText::_('COM_MANDATOS_SL_OPERACION_LIQUIDAR');?></h3>
<div class="table">
    <div class="row">
        <div class="col-md-4">numero de orden</div>
        <div class="col-md-4">Beneficiario</div>
        <div class="col-md-4">monto</div>
    </div>
    <?php
    foreach ($operaciones as $key => $value) {
    ?>
        <div class="row">
            <div class="col-md-4"><?php echo $value->numOrden; ?></div>
            <div class="col-md-4"><?php echo $value->beneficiary->tradeName; ?></div>
            <div class="col-md-4">$<?php echo number_format($value->totalAmount,2); ?></div>
        </div>
    <?php
    }
    ?>
</div>