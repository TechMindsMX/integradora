<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$factura   = $this->factura;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';
?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script>
    function cancelar() {
        history.back();
    }
    function send() {
        var divEnviar       = jQuery('#enviar');
        var divConfirmacion = jQuery('#confirmar');

        var paymentDate = jQuery('input[name*="paymentDate"]').val();
        var data = jQuery('#form_admin_odd').serialize();
        if( !(jQuery('#ordenPagada').prop('checked')) ){
            data += '&ordenPagada=';
        }

        data += '&paymentDay='+paymentDate;

        var request = jQuery.ajax({
            url: 'index.php?option=com_facturas&task=factform.safeForm&format=raw',
            data: data,
            type: 'post',
            async: false
        });

        request.done(function(result){
            mensajes(result);
            divEnviar.show();
            divConfirmacion.hide();
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }
    function buscaCuentas() {
        var select      = jQuery('#cuenta');
        var claveBanco  = jQuery(this).val();

        select.find('option').remove();
        select.append('<option value="">Seleccione su Opción</option>');

        var request     = jQuery.ajax({
            url: 'index.php?option=com_facturas&task=cuentas&format=raw',
            data: {'claveBanco': claveBanco},
            type: 'post',
            async: false
        });

        request.done(function(response){
            $dataSelect = response.data;

            jQuery.each($dataSelect, function(key,value){
                select.append('<option value="'+value.id+'">'+value.numCuenta+'</option>');
            });
        });
    }
    function confirmar() {
        var boton           = jQuery(this).prop('id');
        var divEnviar       = jQuery('#enviar');
        var divConfirmacion = jQuery('#confirmar');

        if(boton === 'send'){
            divEnviar.hide();
            divConfirmacion.show();
        }else{
            divEnviar.show();
            divConfirmacion.hide();
        }
    }
    jQuery(document).ready(function(){
        jQuery('#cancel').on('click', cancelar);
        jQuery('#send').on('click', confirmar);
        jQuery('#confirm').on('click', send);
        jQuery('#noConfirm').on('click', confirmar);
        jQuery('#banco').on('change', buscaCuentas);
    });
</script>

<div>
    <div class="col-forms-ordenes facturas"><h3><?php echo JText::_('COM_FACTURAS_FORM_FACTURA_FOLIO').':</h3> '.$factura->id; ?></div>
    <div class="col-forms-ordenes facturas"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN').':</h3> $'.number_format($factura->Comprobante->total,2 ); ?></div>
    <div class="col-forms-ordenes facturas"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN').':</h3> '.$factura->Comprobante->fecha; ?></div>
    <div class="col-forms-ordenes facturas"><h3><?php echo JText::_('COM_FACTURAS_FORM_FACTURA_EMISOR').':</h3> '.$factura->Emisor->nombre; ?></div>
    <div class="col-forms-ordenes facturas"><h3><?php echo JText::_('COM_FACTURAS_FORM_FACTURA_RECEPTOR').':</h3> '.$factura->Receptor->nombre; ?></div>
</div>

<div class="clearfix">&nbsp;</div>

<form id="form_admin_odd" class="form" method="post">
    <div class="form-group marcarOrden">
        <label for="ordenPagada">
            <h3><?php echo JText::_('COM_FACTURAS_FROM_FACTURA_PAGADA'); ?> <span style="color: #FF0000">*</span>
                <input type="checkbox" id="ordenPagada" name="ordenPagada" value="1" >
            </h3>
        </label>
    </div>
    <div class="clearfix">&nbsp;</div>

    <h2><?php echo JText::_('COM_FACTURAS_FROM_ODD_TRANSACCION'); ?></h2>

    <div class="form-group">
        <label for="banco"><?php echo JText::_('COM_FACTURAS_FROM_ODD_BANCO'); ?> <span style="color: #FF0000;">*</span> </label>
        <select name="banco" id="banco">
            <option value="">Seleccione su opción</option>
            <?php
            foreach ($this->numcuentas['select'] as $banco=>$clave){
                echo '<option value="'.$clave.'">'.$banco.'</option>';
            }
            ?>
        </select>

        <label for="cuenta"><?php echo JText::_('COM_FACTURAS_FROM_ODD_CUENTAS'); ?> <span style="color: #FF0000;">*</span> </label>
        <select name="cuenta" id="cuenta">
            <option value="">Seleccione su opción</option>
        </select>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference"><?php echo JText::_('COM_FACTURAS_FROM_ODD_REFERENCIA'); ?> <span style="color: #FF0000;">*</span> </label>
        <input type="text" maxlength="21" name="reference" id="reference">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="paymentDate"><?php echo JText::_('COM_FACTURAS_FROM_ODD_FECHA_CONCILIACION'); ?> <span style="color: #FF0000;">*</span> </label>
        <?php
        $default = date('Y-m-d');
        echo JHTML::_('calendar',$default,'paymentDate', 'paymentDay', $format = '%Y-%m-%d', $attsCal);
        ?>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference"><?php echo JText::_('COM_FACTURAS_FROM_ODD_MONTO'); ?> <span style="color: #FF0000;">*</span> </label>
        <input type="text" name="amount" id="amount">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <div id="enviar">
            <input type="button" class="btn btn-danger" value="Cancelar" id="cancel">
            <input type="button" class="btn btn-primary" value="Enviar" id="send"/>
        </div>
        <div id="confirmar">
            <div class="sure">¡Esta seguro de realizar la Conciliación!</div>
            <input type="button" class="btn btn-danger" value="Cancelar" id="noConfirm">
            <input type="button" class="btn btn-success" value="Confirmar" id="confirm"/>
        </div>
    </div>
</form>