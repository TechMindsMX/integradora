<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$sesion = JFactory::getSession();
$nuevoSaldo = $sesion->set('nuevoSaldo',0, 'solicitudliquidacion');
$sesion->clear('nuevoSaldo','solicitudliquidacion');
$sesion->clear('idTx','solicitudliquidacion');

echo '<script src="libraries/integradora/js/tim-validation.js"> </script>';

$operaciones = $this->operaciones;
$saldo = $this->saldo;
$saldo->subtotalTotalOperaciones = $nuevoSaldo == 0 ? $saldo->subtotalTotalOperaciones : $nuevoSaldo;

?>
<script>

    function validate() {
        var data = jQuery('#form_solicitudLiquidacion').serialize();

        var request = jQuery.ajax({
            url: "index.php?option=com_mandatos&task=solicitudliquidacion.validateform&format=raw",
            data: data,
            type: 'post',
            async: false
        });

        request.done(function(result){
            mensajesValidaciones(result);

            if(result.success){
	            var boton          = jQuery(this);
	            var campoMonto     = jQuery('#monto');
	            var campoMontoText = jQuery('#montoText');
	            var spanBoton      = jQuery('#botonSalvado');

	            boton.remove();

	            spanBoton.html('<button type="button" class="btn btn-success span3" id="liquidar">Enviar</button>');
	            campoMonto.prop('type','hidden');
	            campoMontoText.text('$'+campoMonto.val()).show();

	            jQuery('#clear_form').remove();
	            jQuery('#liquidar').on('click',liquidar);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }

    function liquidar() {
	    var $form = jQuery('#form_solicitudLiquidacion');
		$form.prop( 'action', 'index.php?option=com_mandatos&task=solicitudliquidacion.saveform');
	    $form.prop( 'method', 'post');
		$form.submit();
    }

    function cancel() {
        window.location = 'index.php?option=com_mandatos';
    }

    jQuery(document).ready(function(){
        jQuery('#confirmar').on('click', validate);
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

    <div class="form-group">
        <label for="monto" class="form-control"><?php echo JText::_('COM_MANDATOS_LBL_MONTO_SL'); ?></label>
	    <h3 id="montoText" style="display: none;"></h3>
	    <input type="text" name="monto" id="monto" />
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
            <td><?php echo isset($value->beneficiary->corporateName) ? $value->beneficiary->corporateName : $value->beneficiary->tradeName; ?></td>
            <td>$<?php echo number_format($value->subTotalAmount,2); ?></td>
            <td>$<?php echo number_format(($value->iva+$value->ieps),2); ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>