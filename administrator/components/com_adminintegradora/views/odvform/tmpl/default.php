<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$orden   = $this->orden;
$data = $this->data;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

$option = '';
if ( ! empty( $this->txs ) ) {
    foreach ($this->txs as $txs) {
        $option .= '<option value="'.$txs->id.'">'.$txs->referencia.' - $'.number_format($txs->balance,2).'</option>';
    }
}
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

<?php if( is_null($data->confirmacion) ){?>
    <div class="div-formODC">
        <form id="form_admin_odd" class="form" method="post" action="index.php?option=com_adminintegradora&view=odvform&confirmacion=1&idOrden=<?php echo $orden->id; ?>" autocomplete="off">
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
                <label for="idTx"><?php echo JText::_('COM_FACTURAS_FROM_ODD_TXS'); ?></label>
                <select name="idTx" id="idTx">
                    <option value="0">Seleccione su opción</option>
                    <?php echo $option; ?>
                </select>
            </div>

            <div class="clearfix">&nbsp;</div>

            <div class="form-group">
                <a href="index.php?option=com_adminintegradora&view=odvlist" class="btn btn-danger">Cancelar</a>
                <input type="submit" class="btn btn-primary" value="Enviar" id="send"/>
            </div>
        </form>
    </div>
    <div class="div-detalleODC">
        <div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN').':</h3> '.$orden->numOrden; ?></div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN').':</h3> $'.number_format($orden->totalAmount,2 ); ?></div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_SALDO_ORDEN').':</h3> $'.number_format($orden->balance,2 ); ?></div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN').':</h3> '.$orden->createdDate; ?></div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ODV_INTEGRADO_EMISOR').':</h3> '.$orden->integradoName; ?></div>
            <div class="col-forms-ordenes"><h3><?php echo JText::_('COM_MANDATOS_ODV_INTEGRADO_RECEPTOR').':</h3> '.$orden->proveedor->frontName; ?></div>
        </div>
        <div class="clearfix"></div>
        <div class="div-detalleProductos">
            <h3>Detalle de orden</h3>
            <table class="table">
                <tr>
                    <th>Cantidad</th>
                    <th>Descripcion</th>
                    <th>Precio Unitario</th>
                    <th>Sub-Total</th>
                    <th>Impuestos</th>
                    <th>Total</th>
                </tr>
                <?php
                foreach($orden->productosData as $value){
                    $subTotal = $value->cantidad*$value->p_unitario;
                    $montoIva = $subTotal*((INT)$value->iva/100);
                    ?>
                    <tr>
                        <td><?php echo $value->cantidad; ?></td>
                        <td><?php echo $value->descripcion; ?></td>
                        <td>$<?php echo number_format($value->p_unitario,2); ?></td>
                        <td>$<?php echo number_format( $subTotal,2 ); ?></td>
                        <td>$<?php echo number_format($montoIva,2); ?></td>
                        <td>$<?php echo number_format($subTotal + $montoIva,2); ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
<?php }else{ ?>
    <form id="confirmacion" method="post" action="index.php?option=com_adminintegradora&task=conciliatxorder.save" autocomplete="off">
        <input type="hidden" name="orderType"   id="orderType"   value="odv" />
        <input type="hidden" name="idOrden"     id="idOrden"     value="<?php echo $orden->id; ?>" />
        <input type="hidden" name="idTx"        id="idTx"        value="<?php echo $data->idTx; ?>" />

        <h3>Esta seguro de guardar los siguientes datos de conciliación</h3>

        <div>Cuenta: <?php echo AdminintegradoraHelper::getBanknameAccount(AdminintegradoraHelper::getBancosIntegradora(new \Integralib\Integrado()), $this->txs[$data->idTx]->cuenta); ?></div>
        <div>Referencia: <?php echo $this->txs[$data->idTx]->referencia; ?></div>
        <div>Fecha: <?php echo date('d-m-Y',$this->txs[$data->idTx]->date); ?></div>
        <div>Monto: $<?php echo number_format($this->txs[$data->idTx]->balance, 2); ?></div>

        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">
            <a href="index.php?option=com_adminintegradora&view=odvlist" class="btn btn-danger">Cancelar</a>
            <input type="submit" id="send" class="btn btn-primary" value="Enviar" />
        </div>

    </form>
<?php } ?>