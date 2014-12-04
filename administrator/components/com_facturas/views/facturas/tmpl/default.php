<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$vName = 'facturas';

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_FACT_COMISIONES'),
    'index.php?option=com_facturas',
    $vName == 'facturas');

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODD'),
    'index.php?option=com_facturas&view=oddlist',
    $vName == 'listadoODD');
JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODC'),
    'index.php?option=com_facturas&view=odclist',
    $vName == 'listadoODC');
JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODR'),
    'index.php?option=com_facturas&view=odrlist',
    $vName == 'listadoODR');

$comision = $this->comision;
?>

<script>
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
                        echo '<option value="'.$value->integrado->integrado_id.'">'.$value->datos_personales->nom_comercial.'</option>';
                    }
                    ?>
                </select>
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
                <th>Estatus</th>
                <th>Integrado</th>
                <th>Fecha</th>
                <th>Folio</th>
                <th>Receptor</th>
                <th>IVA</th>
                <th>Sub-Total</th>
                <th>Total Factura</th>
                <th>Comision</th>
                <th>Total Fact+Comision</th>
                <th>Detalle</th>
            </tr>
            </thead>
            <tbody class="tbody" id="tbody">
            <?php foreach ($this->facturas as $value) {?>
                <tr id="integrado_<?php echo $value->integradoId; ?>" class="row integrado_<?php echo $value->integradoId; ?>">
                    <td><?php echo $value->status; ?></td>
                    <td><?php echo $value->integradoName; ?></td>
                    <td>
                        <?php echo $value->fecha; ?>
                        <input type="hidden" class="fechaNumero" value="<?php echo $value->fechaNum; ?>" />
                    </td>
                    <td><?php echo $value->folio; ?></td>
                    <td><?php echo $value->emisor; ?></td>
                    <td>$<?php echo number_format($value->iva,2); ?></td>
                    <td>$<?php echo number_format($value->subtotal,2); ?></td>
                    <td>$<?php echo number_format($value->total,2); ?></td>
                    <td>$<?php echo number_format($comision,2); ?></td>
                    <td>$<?php echo number_format($value->total+$comision,2); ?></td>
                    <td>&nbsp;</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>
