<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$vName = 'facturas';

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

JSubMenuHelper::addEntry(
    JText::_('Listado de Facturas por cobrar'),
    'index.php?option=com_facturas',
    $vName == 'facturas');
?>

<script>

    function cobrar() {

        jQuery("input[type=checkbox]:checked").each(function(){
                var id=jQuery(this).val();
                var filaFactura=jQuery(this).parent().parent();
                console.log(filaFactura);
            jQuery.ajax({
                type: "POST",
                url: "index.php?option=com_facturasporcobrar&task=factdata.updatefact&format=raw&id="+id,
                async:false,
                    success: function(response){
                        if(response == 'done'){
                            filaFactura.hide();
                        }
                }
            });
        });
    }

    function filtroIntegrado() {
        var idIntegrado = jQuery(this).val();

        if(idIntegrado == 0){
            jQuery('tr[id*="integrado"]').show();
        }else{
            console.log('.integrado_'+idIntegrado);
            jQuery('tr[id*="integrado"]').hide();
            jQuery('.integrado_'+idIntegrado).show();
        }
    }

    jQuery(document).ready(function(){
        jQuery('#integrado').on('change', filtroIntegrado);
    });
</script>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">

<form action="" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <div  class="integrado-id" id="odv">
        <div class="head2" id="head" >
            <div class="filtros" id="columna1" >
                <label for="integrado">Seleciona el Integrado:</label>
                <select id='integrado' name="integrado" class="integrado">
                    <option value="0" selected="selected">Seleccione el filtro</option>
                    <?php
                    foreach ($this->integrados as $key => $value) {
                        echo '<option value="'.$value->integrado->integradoId.'">'.$value->displayName.'</option>';
                    }
                    ?>
                </select>
                <br>
                <input type="button" class="btn btn-primary" value="Marcar Cobrada" id="cobra" onclick="cobrar()">
                <div id="ajax"></div>
            </div>
            <div class="filtros">
                <div class="columna1">
                    <label for="fechaFin">Fecha Inicio</label>
                    <?php
                    $d = new DateTime();
                    $d->modify('first day of this month');
                    $default = $d->format('Y-m-d');
                    echo JHTML::_('calendar',$default,'fechaInicio', 'fechaInicio', $format = '%Y-%m-%d', $attsCal);
                    ?>
                </div>
                <div class="columna1">
                    <label for="fechaFin">Fecha Fin</label>
                    <?php
                    $d = new DateTime();
                    $d->modify('last day of this month');
                    $default = $d->format('Y-m-d');
                    echo JHTML::_('calendar',$default,'fechaFin', 'fechaFin', $format = '%Y-%m-%d', $attsCal);
                    ?>
                </div>
                <div>
                    <input type="button" class="btn btn-primary" value="Buscar" id="filtrofecha">
                    <input type="button" class="btn btn-primary" value="Limpiar" id="llenatabla">
                </div>
            </div>
        </div>
    </div>
    <div id="table_content">
        <table class="adminlist table" id="table_list" cellspacing="0" cellpadding="0">
            <thead class="thead">
            <tr class="row0" id="head" >
                <th></th>
                <th>Integrado</th>
                <th>Fecha</th>
                <th>Folio</th>
                <th>Receptor</th>
                <th>IVA</th>
                <th>Sub-Total</th>
                <th>Total Factura</th>
                <th>Comision</th>
                <th>Total Fact+Comision</th>
                <th>Cobrada</th>
                <th>Detalle</th>
            </tr>
            </thead>
            <tbody class="tbody" id="tbody">
            <?php if(is_array($this->facturas)) {
                foreach ($this->facturas as $value) {
                    ?>
                    <tr id="integrado_<?php echo $value->integradoId; ?>"
                        class="row integrado_<?php echo $value->integradoId; ?>">
                        <td><?php echo $value->integradoName; ?></td>
                        <td>
                            <?php echo $value->createdDate; ?>
                        </td>
                        <td><?php echo $value->numOrden; ?></td>
                        <td><?php echo $value->proveedor->frontName; ?></td>
                        <td>$<?php echo number_format($value->iva, 2); ?></td>
                        <td>$<?php echo number_format($value->subTotalAmount, 2); ?></td>
                        <td>$<?php echo number_format($value->totalAmount, 2); ?></td>
                        <td>$<?php echo number_format($value->comision, 2); ?></td>
                        <td>$<?php echo number_format($value->totalAmount + $value->comision, 2); ?></td>
                        <td><input value="<?php echo $value->id; ?>" type="checkbox" id=""></td>
                        <?php

                        $url_preview = JRoute::_('index.php?option=com_facturasporcobrar&view=facturapreview&integradoId=' . $value->integradoId . '&facturanum=' . $value->id);
                        $preview_button = '<a href="' . $url_preview . '"><i class="icon-search"></i></a>';
                        $btn = '<a class="btn btn-warning" href="index.php?option=com_mandatos&view=facturapreview&layout=confirmcancel&facturanum=' . $value->id . '&integradoId=' . $value->integradoId . '">' . JText::_('COM_MANDATOS_ORDENES_CANCEL_FACT') . '</a>';
                        ?>
                        <td><?php echo $preview_button . $value->id ?></td>
                    </tr>
                <?php }
            }?>
            </tbody>
        </table>
    </div>
</form>
