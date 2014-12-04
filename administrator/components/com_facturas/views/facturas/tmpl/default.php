<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$vName = 'facturas';

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_FACTURAS'),
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

foreach($this->comision as $value) {
    if ($value->description == 'Factura') {
        $comision = $value->monto;
    }
}
foreach($this->facturas as $value) {
    $data = new stdClass();

    $data->fecha        = $value->Comprobante->fechaFormateada;
    $data->folio        = $value->Comprobante->serie . $value->Comprobante->folio;
    $data->emisor       = $value->Emisor->nombre;
    $data->iva          = $value->Impuestos->totalImpuestosTrasladados;
    $data->subtotal     = $value->Comprobante->subTotal;
    $data->total        = $value->Comprobante->total;
    $data->status      = $value->status;
    $data->factComision = $data->total+$comision;

    $dataFacturas[]     = $data;
}
?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">

<form action="" method="post" name="adminForm" id="adminForm">
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
        <table class="adminlist table" id="table_list" cellspacing="0" cellpadding="0" id="odv">
        <thead class="thead">
        <tr class="row0" id="head" >
            <th id="columna1" >Estatus</th>
            <th> Fecha</th>
            <th>Folio</th>
            <th>Emisor</th>
            <th>IVA</th>
            <th>Sub-Total</th>
            <th>Total Factura</th>
            <th>Comision</th>
            <th>Total Fact+Comision</th>
            <th>Detalle</th>
        </tr>
        </thead>
        <tbody class="tbody" id="tbody">
        <?php foreach ($dataFacturas as $value) {?>
            <tr>
                <td><?php echo $value->status; ?></td>
                <td><?php echo $value->fecha; ?></td>
                <td><?php echo $value->folio; ?></td>
                <td><?php echo $value->emisor; ?></td>
                <td>$<?php echo number_format($value->iva,2); ?></td>
                <td>$<?php echo number_format($value->subtotal,2); ?></td>
                <td>$<?php echo number_format($value->total,2); ?></td>
                <td>$<?php echo number_format($comision,2); ?></td>
                <td>$<?php echo number_format($value->factComision,2); ?></td>
                <td></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="10">
                <div class="pagination pagination-toolbar">
                    <input type="hidden" value="0" name="limitstart">
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
    </div>
</form>
