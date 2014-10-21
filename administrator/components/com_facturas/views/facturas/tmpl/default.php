<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$vName = 'facturas';

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_FACTURAS'),
    'index.php?option=com_facturas',
    $vName == 'facturas');

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODD'),
    'index.php?option=com_facturas&view=oddlist',
    $vName == 'listadoODD');

foreach($this->comision as $value);

    if($value->description=='Factura')
    {
        $comision= $value->amount;
    }
foreach($this->facturas as $value) {
    $data->fecha = substr($value->Comprobante->fecha,0,10);
    $data->folio = $value->Comprobante->serie . $value->Comprobante->folio;
    $data->emisor = $value->Emisor->nombre;
    $data->iva = $value->Impuestos->totalImpuestosTrasladados;
    $data->subtotal = $value->Comprobante->subTotal;
    $data->total = $value->Comprobante->total;
    $data->estatus = $value->status;
}
$tot=$data->total+$comision;
?>

<script language="javascript" type="text/javascript">
    var nextinput       = 0;
    var arrayFact       = <?php echo json_encode($this->facturas)?>;
    var comisio        = <?php echo json_encode($comision)?>;
    var comision        = parseFloat(comisio).toFixed(2);


    jQuery('.integrado').on('change', llenatabla);

    function comition(id){
        var status = document.getElementById(id).checked;
        var parent= jQuery('#'+id).parent().parent();
        if(status==true){
            parent.find('#faccomi').html('<?php echo '$'.number_format($tot,2); ?>');
            parent.find('input[name*="fabiccom"]').val('<?php echo '$'.number_format($tot,2); ?>');
        }else{
            parent.find('#faccomi').html('');
            parent.find('input[name*="fabiccom"]').val('');
        }
    }


    function llenatabla() {
        var idintegrado = jQuery('.integrado').val();
        //Limpiamos el Div
        jQuery('#table_content').html('');
        jQuery('#table_content').html('<table id="table_list" class="adminlist table" cellspacing="0" cellpadding="0" >'
        +'<thead class="thead"></thead>'
        +'<tbody class="tbody"></tbody>'
        +'<tfoot>'
        +'<tr>'
        +'<td colspan="10">'
        +'<div class="pagination pagination-toolbar">'
        +'<input type="hidden" value="0" name="limitstart">'
        +'</div>'
        +'</td>'
        +'</tr>'
        +'</tfoot>'
        +'</table>');


        //Encabezados del Div
        jQuery('.thead').append('<tr class="row0" id="head" >'
        +'<th id="columna1" >Estatus</th>'
        +'<th> Fecha</th>'
        +'<th>Folio</th>'
        +'<th>Emisor</th>'
        +'<th>IVA</th>'
        +'<th>Sub-Total</th>'
        +'<th>Total Factura</th>'
        +'<th>Comision</th>'
        +'<th>Total Fact+Comision</th>'
        +'<th>Detalle</th>'
        +'</tr>');

        //Se repite en base a las facturas encontratas en TIMONE
        jQuery.each(arrayFact, function (key, value) {
            nextinput++;
            var fecha           = value.Comprobante.fecha;
            fecha               = fecha.slice(0,10);
            var folio           = value.Comprobante.serie+value.Comprobante.folio;
            folio               = folio.replace(" ","");
            var emisor          = value.Emisor.nombre;
            var idintegrado     = jQuery('.integrado').val();
            var num             = value.Impuestos.Traslados.Traslado.importe;
            var iva             = parseFloat(num).toFixed(2);
            var sub             = value.Comprobante.subTotal;
            var subtotal        =parseFloat(sub).toFixed(2);
            var tot             = value.Comprobante.total;
            var total           =parseFloat(tot).toFixed(2);
            var estatus         =value.status;

            if(estatus== 0){
                if (idintegrado == value.integradoId) {
                    jQuery('.tbody').append('<tr class="row1">'
                    +'<td><input  id="facturar'+nextinput+'" type="checkbox"  onchange="comition(this.id, this.checked);" name="facturar'+nextinput+'" class="facturar" value=""></td>'
                    +'<td><span>'+fecha+'</span><input id="fecha'+nextinput+'" type="hidden" style="width: 70px" name="fecha'+nextinput+'"   value="'+fecha+'"></td>'
                    +'<td><span>'+folio+'</span><input id="folio'+nextinput+'" type="hidden" style="width: 75%" name="folio'+nextinput+'" value="'+folio+'"></td>'
                    +'<td><span>'+emisor+'</span></td>'
                    +'<td><span>$'+iva+'</span><input id="iva'+nextinput+'" type="hidden" style="width: 70px" name="iva'+nextinput+'" value="'+iva+'" class="iva"></td>'
                    +'<td><span>$'+subtotal+'</span><input id="subtotal'+nextinput+'" type="hidden" style="width: 70px" name="subtotal'+nextinput+'" value="'+subtotal+'" class="subtotal"></td>'
                    +'<td ><span>$'+total+'</span><input id="total'+nextinput+'" type="hidden" style="width: 70px"    name="total" value="'+total+'" class="total"></td>'
                    +'<td><span>$'+comision+'</span><input id="comision'+nextinput+'" type="hidden" style="width: 70px"    name="comision'+nextinput+'" value="'+comision+'" class="total"></td>'
                    +'<td ><span id="faccomi"></span><input id="fabiccom" type="hidden" style="width: 70px"    name="fabiccom" value="" class="total"></td>'
                    +'<td ><button type="button" id="detalle_factura" name="button" class="btn btn-primary span3">Ver</button></td>'
                    +'</tr>');
                }
            }

        });
    }

</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <div  class="integrado-id" id="odv">
        <div class="head2" id="head" >
            <div id="columna1" ><span>Seleciona el Integrado:</span>

                <select id='integrado' name="integrado" onchange="llenatabla()" class="integrado">
                    <option value="0"></option>
                    <?php
                    foreach ($this->usuarios as $key => $value) {
                        echo '<option value="'.$value->integrado_id.'">'.$value->name.'</option>';
                    }
                    ?>

                </select>
            </div>

        </div>
    </div>
    <div id="table_content">
        <table class="adminlist table" id="table_list" cellspacing="0" cellpadding="0" id="odv">
        <thead class="thead"></thead>
        <tbody class="tbody"></tbody>
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
