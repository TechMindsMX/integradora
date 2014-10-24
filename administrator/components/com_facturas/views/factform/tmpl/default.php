<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$factura   = $this->factura;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script>
    function cancelar() {
        history.back();
    }
    jQuery(document).ready(function(){
        jQuery('#cancel').on('click', cancelar);
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

<form id="form_admin_odd" class="form">
    <div class="form-group marcarOrden">
        <label for="ordenPagada">
            <h3><?php echo JText::_('COM_FACTURAS_FROM_FACTURA_PAGADA'); ?>
                <input type="checkbox" id="ordenPagada" name="ordenPagada" value="1" >
            </h3>
        </label>
    </div>
    <div class="clearfix">&nbsp;</div>

    <h2><?php echo JText::_('COM_FACTURAS_FROM_ODD_TRANSACCION'); ?></h2>

    <div class="form-group">
        <label for="banco_cuenta"><?php echo JText::_('COM_FACTURAS_FROM_ODD_BANCO'); ?></label>
        <select name="banco_cuenta" id="banco_cuenta">
            <option value="0">Seleccione su opción</option>
        </select>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference"><?php echo JText::_('COM_FACTURAS_FROM_ODD_REFERENCIA'); ?></label>
        <input type="text" maxlength="21" name="reference" id="reference">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="paymentDate"><?php echo JText::_('COM_FACTURAS_FROM_ODD_FECHA_CONCILIACION'); ?></label>
        <?php
        $default = date('Y-m-d');
        echo JHTML::_('calendar',$default,'paymentDate', 'paymentDay', $format = '%Y-%m-%d', $attsCal);
        ?>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference"><?php echo JText::_('COM_FACTURAS_FROM_ODD_MONTO'); ?></label>
        <input type="text" name="reference" id="reference">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="button" class="btn btn-danger" value="Cancelar" id="cancel">
        <input type="button" class="btn btn-primary" value="Enviar" id="send"/>
    </div>
</form>