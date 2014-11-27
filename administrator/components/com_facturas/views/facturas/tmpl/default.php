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
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script language="javascript" type="text/javascript">
    var nextinput       = 0;
    var arrayFact       = <?php echo json_encode($this->facturas)?>;
    var comisio        = <?php echo json_encode($comision)?>;
    var comision        = parseFloat(comisio).toFixed(2);

    jQuery(document).ready(function(){
        llenatabla();
        jQuery('.integrado').on('change', llenatabla);
        jQuery('#llenatabla').on('click', llenatabla);
        jQuery('#filtrofecha').on('click', filtro_fechas);
    });


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

        jQuery('#tbody').find('tr').remove();

        //Se repite en base a las facturas encontratas en TIMONE
        jQuery.each(arrayFact, function (key, value) {
            nextinput++;
            var folio           = value.Comprobante.serie+value.Comprobante.folio;
        	var num             = value.Impuestos.Traslados.Traslado.importe;
        	var sub             = value.Comprobante.subTotal;
        	var tot             = value.Comprobante.total;

            var obj = {fecha           : value.Comprobante.fechaFormateada,
       					timestamp       : value.Comprobante.fechaNumero,
		            	folio           : folio.replace(" ",""),
		            	emisor          : value.Emisor.nombre,
		            	idintegrado     : jQuery('.integrado').val(),
		            	iva             : parseFloat(num).toFixed(2),
		            	subtotal        : parseFloat(sub).toFixed(2),
		            	total           : parseFloat(tot).toFixed(2),
		            	estatus         : value.status
            		}

            if(obj.estatus== 0){
                if (obj.idintegrado == value.integradoId) {
                    jQuery('#tbody').append('<tr class="row1" id="'+nextinput+'_'+value.integradoId+'">'
                    +'<td><input  id="facturar'+nextinput+'" type="checkbox"  onchange="comition(this.id, this.checked);" name="facturar'+nextinput+'" class="facturar" value=""></td>'
                    +'<td><span>'+fecha+'</span><input id="fecha'+nextinput+'" type="hidden" style="width: 70px" name="fecha'+nextinput+'"   value="'+timestamp+'"></td>'
                    +'<td><span>'+folio+'</span><input id="folio'+nextinput+'" type="hidden" style="width: 75%" name="folio'+nextinput+'" value="'+folio+'"></td>'
                    +'<td><span>'+emisor+'</span></td>'
                    +'<td><span>$'+iva+'</span><input id="iva'+nextinput+'" type="hidden" style="width: 70px" name="iva'+nextinput+'" value="'+iva+'" class="iva"></td>'
                    +'<td><span>$'+subtotal+'</span><input id="subtotal'+nextinput+'" type="hidden" style="width: 70px" name="subtotal'+nextinput+'" value="'+subtotal+'" class="subtotal"></td>'
                    +'<td ><span>$'+total+'</span><input id="total'+nextinput+'" type="hidden" style="width: 70px"    name="total" value="'+total+'" class="total"></td>'
                    +'<td><span>$'+comision+'</span><input id="comision'+nextinput+'" type="hidden" style="width: 70px"    name="comision'+nextinput+'" value="'+comision+'" class="total"></td>'
                    +'<td ><span id="faccomi"></span><input id="fabiccom" type="hidden" style="width: 70px"    name="fabiccom" value="" class="total"></td>'
                    +'<td ><button type="button" id="detalle_factura" name="button" class="btn btn-primary">Ver</button></td>'
                        +'<td ><a class="btn btn-primary" href="index.php?option=com_facturas&view=factform&factNum='+value.id+'">Conciliar</a></td>'
                    +'</tr>');
                }
                if(typeof(obj.idintegrado) == 'undefined' || obj.idintegrado == 0){
                    pintaCamposTable(obj, nextinput);
                }

            }

        });
    }
    
    function pintaCamposTable(obj) {
    	jQuery('#tbody').append('<tr class="row1" id="filaintegrado'+value.integradoId+'">'
                        +'<td><input  id="facturar'+nextinput+'" type="checkbox"  onchange="comition(this.id, this.checked);" name="facturar'+nextinput+'" class="facturar" value=""></td>'
                        +'<td><span>'+fecha+'</span><input id="fecha'+nextinput+'" type="hidden" style="width: 70px" name="fecha'+nextinput+'"   value="'+timestamp+'"></td>'
                        +'<td><span>'+folio+'</span><input id="folio'+nextinput+'" type="hidden" style="width: 75%" name="folio'+nextinput+'" value="'+folio+'"></td>'
                        +'<td><span>'+emisor+'</span></td>'
                        +'<td><span>$'+iva+'</span><input id="iva'+nextinput+'" type="hidden" style="width: 70px" name="iva'+nextinput+'" value="'+iva+'" class="iva"></td>'
                        +'<td><span>$'+subtotal+'</span><input id="subtotal'+nextinput+'" type="hidden" style="width: 70px" name="subtotal'+nextinput+'" value="'+subtotal+'" class="subtotal"></td>'
                        +'<td ><span>$'+total+'</span><input id="total'+nextinput+'" type="hidden" style="width: 70px"    name="total" value="'+total+'" class="total"></td>'
                        +'<td><span>$'+comision+'</span><input id="comision'+nextinput+'" type="hidden" style="width: 70px"    name="comision'+nextinput+'" value="'+comision+'" class="total"></td>'
                        +'<td ><span id="faccomi"></span><input id="fabiccom" type="hidden" style="width: 70px"    name="fabiccom" value="" class="total"></td>'
                        +'<td ><button type="button" id="detalle_factura" name="button" class="btn btn-primary ">Ver</button></td>'
                        +'<td ><a class="btn btn-primary" href="index.php?option=com_facturas&view=factform&factNum='+value.id+'">Conciliar</a></td>'
                        +'</tr>');
    }

</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <div  class="integrado-id" id="odv">
        <div class="head2" id="head" >
            <div class="filtros" id="columna1" >
                <label for="integrado">Seleciona el Integrado:</label>
                <select id='integrado' name="integrado" class="integrado">
                    <option value="0" selected="selected">Seleccione el filtro</option>
                    <?php
                    foreach ($this->usuarios as $key => $value) {
                        echo '<option value="'.$value->integrado_id.'">'.$value->name.'</option>';
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
        <tbody class="tbody" id="tbody"></tbody>
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
