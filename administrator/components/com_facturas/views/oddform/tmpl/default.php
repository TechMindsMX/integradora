<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$orden   = $this->orden;
$data = $this->data;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

$option = '';
foreach ($this->txs as $txs) {
    $txs->dateJavascript = date('Y-m-d', ($txs->fechaTimestamp) );
    $option .= '<option value="'.$txs->id.'">'.$txs->referencia.' - $'.number_format($txs->amount,2).'</option>';
}

?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script>
    function cancelar() {
        window.location = 'index.php?option=com_facturas&view=oddlist';
    }

    function cargaData() {
        var idTx = jQuery(this).val();
        var txs = <?php echo json_encode($this->txs); ?>;

        if(idTx != 0) {
            jQuery.each(txs, function (k, v) {
                if (v.id == idTx) {
                    console.log(v);
                    jQuery('#cuenta').val(v.cuenta);
                    jQuery('#referencia').val(v.referencia);
                    jQuery('#date').val(v.dateJavascript);
                    jQuery('#amount').val(v.amount);
                }
            });
        }else{
            jQuery('#cuenta').val('');
            jQuery('#referencia').val('0');
            jQuery('#date').val('');
            jQuery('#amount').val('');
        }
    }

    jQuery(document).ready(function(){
        jQuery('#cancel').on('click', cancelar);
        jQuery('#tx').on('change',cargaData);
    });
</script>
<?php if( is_null($data->confirmacion) ){?>
    <div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN').':</h3> '.$orden->numOrden; ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN').':</h3> $'.number_format($orden->totalAmount,2 ); ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN').':</h3> '.$orden->createdDate; ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ODD_INTEGRADO').':</h3> '.$orden->integradoName; ?></div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <form id="form_admin_odd" class="form" method="post" action="index.php?option=com_facturas&view=oddform&confirmacion=1&idOrden=<?php echo $orden->id; ?>">
        <div class="form-group">
            <label for="ordenPagada">
                <?php echo JText::_('COM_FACTURAS_FROM_ODD_PAGADA'); ?>
                <input type="checkbox" id="ordenPagada" name="ordenPagada" value="1" >
            </label>
        </div>
        <div class="clearfix">&nbsp;</div>

        <h2><?php echo JText::_('COM_FACTURAS_FROM_ODD_TRANSACCION'); ?></h2>

        <div class="form-group">
            <label for="tx"><?php echo JText::_('COM_FACTURAS_FROM_ODD_TXS'); ?></label>
            <select name="tx" id="tx">
                <option value="0">Seleccione su opción</option>
                <?php echo $option; ?>
                </select>
        </div>
        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <label for="banco_cuenta"><?php echo JText::_('COM_FACTURAS_FROM_ODD_BANCO'); ?></label>
            <select name="cuenta" id="cuenta">
                <option value="0">Seleccione su opción</option>
                <option value="1">BBVA - 6622</option>
                <option value="2">BANORTE - 5599</option>
            </select>
        </div>
        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <label for="referencia"><?php echo JText::_('COM_FACTURAS_FROM_ODD_REFERENCIA'); ?></label>
            <input type="text" maxlength="21" name="referencia" id="referencia">
        </div>
        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <label for="paymentDate"><?php echo JText::_('COM_FACTURAS_FROM_ODD_FECHA_CONCILIACION'); ?></label>
            <?php
            $default = date('Y-m-d');
            echo JHTML::_('calendar',$default,'date', 'date', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div>
        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <label for="amount"><?php echo JText::_('COM_FACTURAS_FROM_ODD_MONTO'); ?></label>
            <input type="text" name="amount" id="amount">
        </div>
        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <input type="button" class="btn btn-danger" value="Cancelar" id="cancel">
            <input type="submit" class="btn btn-primary" value="Enviar" id="send"/>
        </div>
    </form>
<?php }else{ ?>
    <form id="confirmacion" method="post" action="index.php?option=com_facturas&view=oddform&task=oddform.save">
        <input type="hidden" name="idOrden" id="idOrden" value="<?php echo $orden->id; ?>" />
        <input type="hidden" name="integradoId" id="integradoId" value="<?php echo $orden->integradoId; ?>" />
        <input type="hidden" name="ordenPagada" id="ordenPagada" value="<?php echo $data->ordenPagada; ?>" />
        <input type="hidden" name="cuenta" id="cuenta" value="<?php echo $data->cuenta; ?>" />
        <input type="hidden" name="referencia" id="referencia" value="<?php echo $data->referencia; ?>" />
        <input type="hidden" name="date" id="date" value="<?php echo $data->date; ?>" />
        <input type="hidden" name="amount" id="amount" value="<?php echo $data->amount; ?>" />

        <h3>Esta seguro de guardar los siguientes datos de conciliación</h3>

        <div>Orden pagada: <?php echo is_null($data->ordenPagada)?'No':'Si'; ?></div>
        <div>Cuenta: <?php echo $data->cuenta; ?></div>
        <div>Referencia: <?php echo $data->referencia; ?></div>
        <div>Fecha: <?php echo $data->date; ?></div>
        <div>Monto: $<?php echo number_format($data->amount, 2); ?></div>

        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">
            <input type="button" id="cancel" class="btn btn-danger" value="Cancelar" />
            <input type="submit" id="send" class="btn btn-primary" value="Enviar" />
        </div>

    </form>
<?php } ?>
