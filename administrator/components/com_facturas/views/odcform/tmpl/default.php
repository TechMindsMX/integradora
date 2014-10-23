<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$orden   = $this->orden;
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
<div class="div-formODC">
    <form id="form_admin_odd" class="form">
        <div class="form-group marcarOrden">
            <label for="ordenPagada">
                <h3><?php echo JText::_('COM_FACTURAS_FROM_ODD_PAGADA'); ?>
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
            <label for="reference"><?php echo JText::_('COM_FACTURAS_FROM_ODD_NUMCHEQUE'); ?></label>
            <input type="text" maxlength="6" name="reference" id="reference">
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
</div>
<div class="div-detalleODC">
    <div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN').':</h3> '.$orden->folio; ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN').':</h3> $'.number_format($orden->totalAmount,2 ); ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN').':</h3> '.$orden->created; ?></div>
        <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ODD_INTEGRADO').':</h3> '.$orden->integradoName; ?></div>
    </div>
    <div class="clearfix"></div>
    <div class="div-detalleProductos">
        <h3>Detalle de orden</h3>
        <table class="table">
            <tr>
                <th>Cantidad</th>
                <th>Descripcion</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
            <?php
            foreach($orden->productos as $value){
                ?>
                <tr>
                    <td><?php echo $value['cantidad']; ?></td>
                    <td><?php echo $value['descripcion']; ?></td>
                    <td>$<?php echo number_format($value['pUnitario'],2); ?></td>
                    <td>$<?php echo number_format( ($value['pUnitario']*$value['cantidad']),2 ); ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>