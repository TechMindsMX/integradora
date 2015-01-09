<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$sesion = JFactory::getSession();
$nuevoSaldo = $sesion->set('nuevoSaldo',0, 'solicitudliquidacion');
$sesion->clear('nuevoSaldo','solicitudliquidacion');
$sesion->clear('idTx','solicitudliquidacion');

echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';

$operaciones = $this->operaciones;
$saldo = $this->saldo;
$saldo->subtotalTotalOperaciones = $nuevoSaldo==0?$saldo->subtotalTotalOperaciones:$nuevoSaldo;
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
            jQuery.each(result, function(k, v){
                if(v != true){
                    mensajes(v.msg,'error',k);
                }
            });

            if(result.success){
                jQuery('#saldoNuevo').text(result.nuevoSaldoText);
                jQuery('#saldoLiquidacion').val(result.nuevoSaldo);

                jQuery('#montoText').hide().text('');
                jQuery('#monto').val('').prop('type','text');

                jQuery('#liquidar').remove()
                jQuery('#botonSalvado').html('<button type="button" class="btn btn-primary span3" id="confirmar">Confirmar</button>');
                jQuery('#confirmar').on('click', confirmar)
            }
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }

    function confirmar() {
        var boton          = jQuery(this);
        var campoMonto     = jQuery('#monto');
        var campoMontoText = jQuery('#montoText');
        var spanBoton      = jQuery('#botonSalvado');

        boton.remove();

        spanBoton.html('<button type="button" class="btn btn-primary span3" id="liquidar">Enviar</button>');
        campoMonto.prop('type','hidden');
        campoMontoText.text(campoMonto.val()).show();

        jQuery('#liquidar').on('click',liquidar);
    }

    function cancel() {
        window.location = 'index.php?option_com_mandatos&view=solicitudliquidacion';
    }
    jQuery(document).ready(function(){
        jQuery('#monto').on('change', validaMonto);
        jQuery('#confirmar').on('click', confirmar);
        jQuery('#cancel_form').on('click', cancel);
    });
</script>
<h1 style="margin-bottom: 40px;"><?php echo JText::_('COM_MANDATOS_GO_LIQUIDACION'); ?></h1>
<form id="form_solicitudLiquidacion">
    <div>
        <h4><?php echo JText::_('COM_MANDATOS_LIQUIDACION_SALDO').': $<span id="saldoNuevo">'.number_format($saldo->subtotalTotalOperaciones,2); ?></span></h4>
        <h4><?php echo JText::_('COM_MANDATOS_SALDO_IMPUESTOS').': $'.number_format($saldo->totalImpuestos,2); ?></h4>
        <input type="hidden" id="saldoLiquidacion" class="saldoliquidacion" name="saldo"       value="<?php echo $saldo->subtotalTotalOperaciones; ?>" />
        <input type="hidden" id="integradoId"      class="integradoId"      name="integradoId" value="<?php echo $this->integradoId; ?>" />
    </div>

    <div>
        <label for="monto"><?php echo JText::_('COM_MANDATOS_LBL_MONTO_SL'); ?></label>
        <input type="text" name="monto" id="monto" />
        <span id="montoText" style="display: none;"></span>
    </div>

    <div class="form-actions" style="max-width: 30%">
        <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
        <span id="botonSalvado"><button type="button" class="btn btn-primary span3" id="confirmar"><?php echo JText::_('LBL_CONFIRMAR'); ?></button></span>
        <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
    </div>
</form>

<h3><?php echo JText::_('COM_MANDATOS_SL_OPERACION_LIQUIDAR');?></h3>
<table class="table">
    <thead>
    <tr>
        <th>&nbsp;</th>
        <th>Número de Orden</th>
        <th>Beneficiario</th>
        <th>Subtotal</th>
        <th>Impuestos</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($operaciones as $key => $value) {
        ?>
        <tr class="row">
            <td><?php echo $value->numOrden; ?></td>
            <td><?php echo $value->beneficiary->corporateName; ?></td>
            <td>$<?php echo number_format($value->subTotalAmount,2); ?></td>
            <td>$<?php echo number_format(($value->iva+$value->ieps),2); ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>