<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');

foreach($this->comision as $value);

    if($value->description=='Factura')
    {
        $comision= $value->amount;
    }
foreach($this->facturas as $value) {
    $data->fecha = $value->Comprobante->fecha;
    $data->folio = $value->Comprobante->serie . $value->Comprobante->folio;
    $data->emisor = $value->Emisor->nombre;
    $data->iva = $value->Impuestos->totalImpuestosTrasladados;
    $data->subtotal = $value->Comprobante->subTotal;
    $data->total = $value->Comprobante->total;
    $data->estatus = $value->status;
}
?>

<script language="javascript" type="text/javascript">
    var nextinput       = 0;
    var arrayFact       = <?php echo json_encode($this->facturas)?>;
    var comision        = <?php echo json_encode($comision)?>;
    jQuery('.integrado').on('change', llenatabla);

    function comition(id){
        var status = document.getElementById(id).checked;
        var parent= jQuery('#'+id).parent().parent();
        if(status==true){
            var comision =parseFloat(parent.find('input[name*="comision"]'));
            var subtotal    = parseFloat(parent.find('input[name*="subtotal"]'));
            var iva         = parseFloat(parent.find('input[name*="iva"]').val());
            var tot         = comision+subtotal+iva;


            parent.find('input[name*="ftotal"]').val(tot);


        }

        /*
        var comision1    = parseInt(parent.find('[name*="comision"]').val());
        var subtotal1    = parseInt(parent.find('[name*="subtotal"]').val());
        var iva1         = parseInt(parent.find('[name*="iva"]').val());
        var tot         = comision1+subtotal1+iva1;
        parent.find('[name*="ftotal"]').val(tot);*/


    }


    function llenatabla() {
        var idintegrado = jQuery('.integrado').val();

        //Limpiamos el Div

        jQuery('.table').html('<table class="adminlist table" cellspacing="0" cellpadding="0" id="odv">'
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
        +'<th id="columna1" >Fecha</th>'
        +'<th id="columna1" >Folio</th>'
        +'<th id="columna1" >Emisor</th>'
        +'<th id="columna1" >IVA</th>'
        +'<th id="columna1">Sub-Total</th>'
        +'<th id="columna1" >Comision</th>'
        +'<th id="columna1" >Total Factura</th>'
        +'<th id="columna1" >Total Fact+Comision</th>'
        +'<th id="columna1" >Detalle</th>'
        +'</tr>');


        //Se repite en base a las facturas encontratas en TIMONE
        jQuery.each(arrayFact, function (key, value) {
            nextinput++;
            if (idintegrado == value.IntegradoId) {
                factura         = value;
                fecha           = factura.Comprobante.fecha;
                fecha           = fecha.slice(0,10);
                folio           = factura.Comprobante.serie+factura.Comprobante.folio;
                emisor          = factura.Emisor.nombre;
                iva             = factura.Impuestos.totalImpuestosTrasladados;
                subtotal        = factura.Comprobante.subTotal;
                total           = factura.Comprobante.total;
                estatus         = factura.status;


                jQuery('.tbody').append('<tr class="row1">'
                +'<td><input  id="facturar'+nextinput+'" type="checkbox"  onchange="comition(this.id, this.checked);" name="facturar'+nextinput+'" class="facturar" value=""></td>'
                +'<td><span><?php echo $data->fecha; ?></span><input id="fecha'+nextinput+'" type="hidden" style="width: 70px" name="fecha'+nextinput+'"   value="<?php echo $data->fecha; ?>"></td>'
                +'<td><span><?php echo $data->folio; ?></span><input id="folio'+nextinput+'" type="hidden" style="width: 75%" name="folio'+nextinput+'" value="<span><?php echo $data->fecha; ?>"></td>'
                +'<td><span><?php echo $data->emisor; ?></span><input id="emisor'+nextinput+'" type="hidden" name="emisor'+nextinput+'" class="emisor" value="<span><?php echo $data->emisor; ?>"></td>'
                +'<td><span><?php echo '$'.number_format($data->iva,2); ?></span><input id="iva'+nextinput+'" type="hidden" style="width: 70px" name="iva'+nextinput+'" value="<span><?php echo $data->iva; ?>" class="iva"></td>'
                +'<td><span><?php echo '$'.number_format($data->subtotal,2); ?></span><input id="subtotal'+nextinput+'" type="hidden" style="width: 70px" name="subtotal'+nextinput+'" value="'+subtotal+'" class="subtotal"></td>'
                +'<td><span><?php echo '$'.number_format($comision,2); ?></span><input id="comision'+nextinput+'" type="hidden" style="width: 70px"    name="comision'+nextinput+'" value="'+comision+'" class="total"></td>' +
                '<td ><span></span><input id="total'+nextinput+'" type="hidden" style="width: 70px"    name="total'+nextinput+'" value="'+total+'" class="total"></td>' +
                '<td ><input id="ftotal" type="text" style="width: 110px"  name="ftotal"  class="total"></td>' +
                '<td ><button type="button" id="detalle_factura" name="button" class="btn btn-primary span3">Ver</button></td>'
                +'</tr>');

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
                    foreach ($this->usuarios as $value) {
                        echo '<option value="'.$value['integrado_id'].'">'.$value['name'].'</option>';
                    }
                    ?>
                </select>
            </div>

        </div>
    </div>
    <table class="adminlist table" cellspacing="0" cellpadding="0" id="odv">
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
</form>
