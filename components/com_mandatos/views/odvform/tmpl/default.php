<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';
?>
<script>
    var productsTypeahead   = new Array();
    <?php
    foreach ($this->products as $key => $value) {
        echo 'productsTypeahead['.$key.'] = "'.$value->productName.'";'."\n";
    }

    ?>
    var subprojects     = <?php echo json_encode($this->proyectos['subproyectos']);?>;
    var global_var      = 0;
    var arrayProd       = <?php echo json_encode($this->products)?>;
    var nextinput       = 0;
    var pre             = "";
    var precio          = 0;
    var total           = 0;
    var typeaheadSettings = {
        source: function (typeahead, query) {
            /* put your ajax call here..
             return $.get('/typeahead', { query: query }, function (data) {
             return typeahead.process(data);
             });
             */
            return productsTypeahead;
        },
        minLength:3
    };

    jQuery(document).ready(function() {
        jQuery('.typeahead').typeahead(typeaheadSettings); /* init first input */

        jQuery('#project').on('change', llenasubproject);
        jQuery('.productos').on('focusout', llenatabla);
        jQuery('#button').on('click', addrow);
        jQuery('.cantidad').on('change', sum);
        jQuery('button').on('click', envio);
        jQuery.each(arrayProd, function (key, value) {
            jQuery('#productos').append('<option value="' + value.id_producto + '">' + value.productName + '</option>');
        });
    });

    function sum(){
        var cantidad    = jQuery(this).val();
        var trproductos = jQuery(this).parent().parent();
        var subtotal    = 0;
        var total       = 0;
        var montoIva    = 0;
        var montoIeps   = 0;

        var precio      = trproductos.find('.p_unit').val();
        var iva         = trproductos.find('.iva').val();
        var ieps        = trproductos.find('.ieps').val();

        cantidad        = parseInt(cantidad);
        precio          = parseInt(precio);
        iva             = parseInt(iva);
        ieps            = parseInt(ieps)==0?null:parseInt(ieps);

        console.log(precio, iva, ieps);

        if(isNaN(iva)  || isNaN(precio)){
            alert("Seleccione primero el producto");
        }else{
            subtotal = precio*cantidad;
            montoIeps = subtotal*(ieps/100);
            montoIva = subtotal*(iva/100);
            total = subtotal+montoIeps+montoIva;

            trproductos.find('#subtotal').html('$'+subtotal);
            trproductos.find('#total').html('$'+total);
        }
    }

    function addrow(){
        nextinput++;

        jQuery("#contenidos" ).attr('id','content'+nextinput+'');
        jQuery("#content"+nextinput+"").clone().appendTo( "#odv");
        jQuery("#content"+nextinput+"").attr('id','contenidos');


        jQuery("#content"+nextinput+"").find("input:text").val("");
        jQuery("#content"+nextinput+"").find("#subtotal").html("");
        jQuery("#content"+nextinput+"").find("#total").html("");


        var select = jQuery("#content"+nextinput+"").find('select');
        var inputs = jQuery("#content"+nextinput+"").find('input');
        var nameCampoS = select.prop('name');
        var nameCampoI = '';

        select.prop('name',nameCampoS+nextinput);
        select.prop('id',nameCampoS+nextinput);

        jQuery.each(inputs, function(k,v){
            nameCampoI = jQuery(v).prop('name');
            jQuery(v).prop('name', nameCampoI+nextinput);
            jQuery(v).prop('id', nameCampoI+nextinput);
        });

        jQuery('.typeahead').trigger('added');
        jQuery('.typeahead').typeahead(typeaheadSettings);
        jQuery('.productos').on('focusout', llenatabla);
        jQuery('.cantidad').on('change', sum);
    }

    function llenasubproject() {
        var select = jQuery(this).val();
        var selectSPro = jQuery('#subproject')
        var subprojectos = subprojects[select];

        jQuery.each(jQuery('#subproject').find('option'), function () {
            jQuery(this).remove();
        });

        selectSPro.append('<option value="0">Subproyecto</option>');

        jQuery.each(subprojectos, function (key, value) {
            selectSPro.append('<option value="' + value.id_proyecto + '">' + value.name + '</option>');
        });
    }

    function llenatabla() {
        var campoproducto   = jQuery(this);
        var valorCampo      = campoproducto.val();
        var parentsCampo    = campoproducto.parents('div.contenidos');
        var campoDescrip    = parentsCampo.find('input[name*="descripcion"]');
        var campoUnidad     = parentsCampo.find('input[name*="unidad"]');
        var campoP_unit     = parentsCampo.find('input[name*="p_unitario"]');
        var campoIva        = parentsCampo.find('input[name*="iva"]');
        var campoIeps       = parentsCampo.find('input[name*="ieps"]');

        var request = jQuery.ajax({
            url: "index.php?option=com_mandatos&task=searchProducts&format=raw",
            data: {
                'productName': valorCampo,
                'integradoId': <?php echo $this->integradoId; ?>
            },
            type: 'post',
            async: false
        });

        request.done(function(response){
            if(response.success){
                var datos = response.datos;
                campoDescrip.val(datos.description);
                campoUnidad.val(datos.measure);
                campoP_unit.val(datos.price);
                campoIva.val(datos.iva);
                campoIeps.val(datos.ieps);
            }else{
                alert(response.msg)
            }
        });
    }

    function envioAjax(data) {
        var request = jQuery.ajax({
            url: "index.php?option=com_mandatos&task=odvform.safeform&format=raw",
            data: data,
            type: 'post',
            async: false
        });

        request.done(function(result){
            var odv = eval('('+result.odv+')');

            if(typeof(odv) == 'undefined' ){
                jQuery.each(result, function(k, v){
                    if(v != true){
                        mensajes(v.msg,'error',k);
                    }
                });
            }else{
                if(result.redirect){
                    jQuery(location).attr('href',result.url);
                }
            }
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }

    function envio() {
        var boton = jQuery(this).prop('id');
        var data = jQuery('#altaC_P').serialize();

        switch (boton){
            case 'seleccion':
                data += '&tab='+boton;
                envioAjax(data);
                break;
            case 'ordenVenta':
                data += '&tab='+boton;
                envioAjax(data);
                break;
        }
    }
</script>

<form action="" class="form" id="altaC_P" name="altaC_P" method="post" enctype="multipart/form-data" >
<?php
echo JHtml::_('bootstrap.startTabSet', 'tabs-odv', array('active' => 'seleccion'));
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'seleccion', JText::_('COM_MANDATOS_ODV_SELECCION'));
?>

<fieldset>
    <select name="projectId" id="project">
        <option value="0">Proyecto</option>
        <?php
        foreach ($this->proyectos['proyectos'] as $key => $value) {
            echo '<option value="'.$value->id_proyecto.'">'.$value->name.'</option>';
        }
        ?>
    </select>

    <select name="projectId2" id="subproject">
        <option value="0">Subproyecto</option>
    </select>

    <select name="clientId">
        <option value="0">Cliente</option>
        <?php
        foreach ($this->clientes as $key => $value) {
            echo '<option value="'.$value->id.'">'.$value->tradeName.'</option>';
        }
        ?>
    </select>
</fieldset>

<div class="form-actions" style="max-width: 30%">
    <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    <button type="button" class="btn btn-primary span3" id="seleccion"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'ordeventa', JText::_('COM_MANDATOS_ODV_ODV'));
?>
<fieldset>
    <select name="account">
        <option value="0">Cuenta</option>
        <?php
        $datosBancarios = $this->solicitud->datos_bancarios;
        echo '<option value="'.$datosBancarios->datosBan_id.'">'.$datosBancarios->banco_cuenta.'</option>';
        ?>
    </select>

    <select name="paymentMethod">
        <option value="0">Método de pago</option>
        <option value="1">Cheque</option>
        <option value="2">Transferencia</option>
        <option value="3">Efectivo</option>
    </select>

    <select name="conditions">
        <option value="0">Condiciones</option>
        <option value="1">Contado</option>
        <option value="2">Parcialidades</option>
    </select>

    <select name="placeIssue">
        <option value="0">Lugar de Expedición</option>
        <?php
        foreach ($this->estados as $key => $value) {
            echo '<option value="'.$value->id.'">'.$value->nombre.'</option>';
        }
        ?>
    </select>

    <h3><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h3>
    <div  class="table table-bordered" id="odv">
        <div class="head" id="head" >
            <div id="columna1" ><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_TITULO'); ?></div>
            <div id="columna1" ><?php echo JText::_('LBL_CANTIDAD'); ?></div>
            <div id="columna1" ><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></div>
            <div id="columna1" ><?php echo JText::_('LBL_UNIDAD'); ?></div>
            <div id="columna1" ><?php echo JText::_('LBL_P_UNITARIO'); ?></div>
            <div id="columna1" ><?php echo JText::_('LBL_SUBTOTAL'); ?></div>
            <div id="columna1"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?></div>
            <div id="columna1" ><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?></div>
            <div id="columna1" ><?php echo JText::_('LBL_TOTAL'); ?></div>
        </div>
        <div class="contenidos" id="contenidos">
            <div id="columna2">
                <input type="text" id="field" placeholder="Ingrese el nombre del producto" class="typeahead productos" data-items="3">
            </div>
            <div id="columna2"><input id="cantidad" type="text" name="cantidad" class="cantidad cantidades" ></div>
            <div id="columna2"><input id="descripcion" type="text" name="descripcion"></div>
            <div id="columna2"><input id="unidad" type="text" name="unidad" class="cantidades"></div>
            <div id="columna2"><input id="p_unitario" type="text" name="p_unitario" class="p_unit cantidades" ></div>
            <div id="columna2"><div id="subtotal"></div></div>
            <div id="columna2"><input id="iva" type="text" name="iva" value="" class="iva cantidades"></div>
            <div id="columna2"><input id="ieps" type="text" name="ieps" value="" class="ieps cantidades"></div>
            <div id="columna2"><div id="total"></div> </div>
        </div>
    </div>

    <div class="clearfix">
        <button type="button" id="button" class="btn btn-success" name="button"> + </button>
    </div>

</fieldset>

<div class="form-actions" style="max-width: 30%">
    <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    <button type="button" class="btn btn-primary span3" id="ordenVenta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.endTabSet');
echo JHtml::_('form.token');
?>

</form>