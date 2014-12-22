<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$orden = $this->orden;
$productosOrden = json_decode($orden->productos);
var_dump($orden);
?>
<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>

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
        source: function () {
            return productsTypeahead;
        },
        minLength:3
    };

    jQuery(document).ready(function() {
        jQuery('.typeahead').typeahead(typeaheadSettings); /* init first input */

        jQuery('#project').on('change', llenasubproject);
        jQuery('.productos').on('change', llenatabla);
        jQuery('#button').on('click', addrow);
        jQuery(document).on('change', '.cantidad, .iva, .ieps, .p_unit', sum);
        jQuery('button').on('click', envio);
        jQuery.each(arrayProd, function (key, value) {
            jQuery('#productos').append('<option value="' + value.id_producto + '">' + value.productName + '</option>');
        });

        <?php
       if($orden->projectId != ''){
       ?>
        jQuery('#project').trigger('change');
        jQuery('.cantidad').trigger('change');
        jQuery('#subproject').val(<?php echo $orden->projectId2; ?>);
        <?php
        }
        ?>
    });

    function sum(){
        var columna    = jQuery(this).parent().parent();
        var cantidad    = columna.find('.cantidad').val();
        var precio      = columna.find('.p_unit').val();
        var iva         = columna.find('.iva').val();
        var ieps        = columna.find('.ieps').val();
        var subtotal    = 0;
        var total       = 0;
        var montoIva    = 0;
        var montoIeps   = 0;

        cantidad        = isNaN(parseFloat(cantidad))?0:parseFloat(cantidad);
        precio          = isNaN(parseFloat(precio))?0:parseFloat(precio);
        iva             = isNaN(parseFloat(iva))?0:parseFloat(iva);
        ieps            = isNaN(parseFloat(ieps))?0:parseFloat(ieps);

        subtotal = precio*cantidad;
        montoIeps = subtotal*(ieps/100);
        montoIva = subtotal*(iva/100);
        total = subtotal+montoIeps+montoIva;

        columna.find('#subtotal').html('$'+subtotal);
        columna.find('#total').html('$'+total);
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

        select.prop('id',nameCampoS+nextinput);

        jQuery.each(inputs, function(k,v){
            nameCampoI = jQuery(v).prop('name');
            jQuery(v).prop('id', nameCampoI+nextinput);
        });

        jQuery('.typeahead').trigger('added');
        jQuery('.typeahead').typeahead(typeaheadSettings);
        jQuery('.productos').on('focusout', llenatabla);
        jQuery('.cantidad').on('change', sum);
        jQuery('.iva').on('change',sum);
        jQuery('.ieps').on('change',sum);
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
            if(result.success){
                jQuery('#idOrden').val(result.id);
                jQuery('#numOrden').html(result.numOrden);
                jQuery('input[name="numOrden"]').val(result.numOrden);
                jQuery('a[href="#'+result.tab+'"]').trigger('click');

                if(result.redirect != null){
                    window.location = result.redirect;
                }
            } else if (!result.success) {
	            jQuery('#altaODV').prepend('<div class="alert alert-error"><a data-dismiss="alert" class="close">×</a><h4 class="alert-heading">Error</h4><div><p>Faltan los productos</p></div></div>')
            }
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    }

    function envio() {
        var boton = jQuery(this).prop('id');
        var data = jQuery('#altaODV').serialize();

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

<form action="" class="form" id="altaODV" name="altaODV" method="post" enctype="multipart/form-data" >
    <h1>Generación de Orden de Venta</h1>
    <h3>Número de Orden: <span id="numOrden"><?php echo $orden->numOrden; ?></span></h3>

    <input type="hidden" name="numOrden" value="<?php echo $orden->numOrden; ?>">
    <input type="hidden" name="integradoId" id="IntegradoId" value="<?php echo $this->integradoId; ?>" />
    <input type="hidden" name="idOrden" id="idOrden" value="<?php echo $orden->id ?>" />
    <?php
    echo JHtml::_('bootstrap.startTabSet', 'tabs-odv', array('active' => 'seleccion'));
    echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'seleccion', JText::_('COM_MANDATOS_ODV_SELECCION'));
    ?>

    <fieldset>
        <select name="projectId" id="project">
            <option value="0">Proyecto</option>
            <?php
            foreach ($this->proyectos['proyectos'] as $key => $value) {
                $selected = $value->id_proyecto==$orden->projectId?'selected':'';
                echo '<option value="'.$value->id_proyecto.'" '.$selected.'>'.$value->name.'</option>';
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
                $selectedCli = ($value->id==$orden->clientId)?'selected':'';
                echo '<option value="'.$value->id.'" '.$selectedCli.'>'.$value->tradeName.'</option>';
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
    echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'ordenventa', JText::_('COM_MANDATOS_ODV_ODV'));
    ?>

    <fieldset>
        <?php
        ?>
        <select name="account">
            <option value="0">Cuenta</option>
            <?php
            foreach ($this->cuentas as $datosCuenta) {
                $selectedCuentas = ( $datosCuenta->datosBan_id == $orden->account) ? 'selected' : '';
                echo '<option value="' . $datosCuenta->datosBan_id . '" '.$selectedCuentas.'>' . $datosCuenta->banco_cuenta_xxx . '</option>';
            }
            ?>
        </select>

        <select name="paymentMethod">
            <option value="0" <?php echo $orden->paymentMethod->id==0?'selected':''; ?>>Método de pago</option>
            <option value="1" <?php echo $orden->paymentMethod->id==1?'selected':''; ?>>Cheque</option>
            <option value="2" <?php echo $orden->paymentMethod->id==2?'selected':''; ?>>Transferencia</option>
            <option value="3" <?php echo $orden->paymentMethod->id==3?'selected':''; ?>>Efectivo</option>
            <option value="4" <?php echo $orden->paymentMethod->id==4?'selected':''; ?>>No Definido</option>
        </select>

        <select name="conditions">
            <option value="0" <?php echo $orden->conditions==0?'selected':''; ?>>Condiciones</option>
            <option value="1" <?php echo $orden->conditions==1?'selected':''; ?>>Contado</option>
            <option value="2" <?php echo $orden->conditions==2?'selected':''; ?>>Parcialidades</option>
        </select>

        <select name="placeIssue">
            <option value="0">Lugar de Expedición</option>
            <?php
            foreach ($this->estados as $key => $value) {
                $selectedPlace = $value->id==$orden->placeIssue?'selected':'';
                echo '<option value="'.$value->id.'" '.$selectedPlace.'>'.$value->nombre.'</option>';
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
                <div id="columna1" ><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?></div>
                <div id="columna1" ><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?></div>
                <div id="columna1" ><?php echo JText::_('LBL_TOTAL'); ?></div>
            </div>
            <?php
            if(is_array($productosOrden)) {
                foreach ($productosOrden as $key => $value) {
                    ?>
                    <div class="contenidos" id="content<?php echo $key + 1000; ?>">
                        <div id="columna2">
                            <input type="text" name="producto[]"
                                   id="producto[]<?php echo $key + 1000; ?>"
                                   placeholder="Ingrese el nombre del producto"
                                   class="typeahead productos"
                                   data-items="3"
                                   value="<?php echo $value->name; ?>">
                        </div>
                        <div id="columna2">
                            <input id="cantidad[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="cantidad[]"
                                   class="cantidad cantidades"
                                   value="<?php echo $value->cantidad; ?>">
                        </div>
                        <div id="columna2">
                            <input id="descripcion[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="descripcion[]"
                                   value="<?php echo $value->descripcion; ?>">
                        </div>
                        <div id="columna2">
                            <input id="unidad[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="unidad[]"
                                   class="cantidades"
                                   value="<?php echo $value->unidad; ?>">
                        </div>
                        <div id="columna2">
                            <input id="p_unitario[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="p_unitario[]"
                                   class="p_unit cantidades"
                                   value="<?php echo $value->p_unitario; ?>">
                        </div>
                        <div id="columna2">
                            <div id="subtotal"></div>
                        </div>
                        <div id="columna2">
                            <input id="iva[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="iva[]"
                                   class="iva cantidades"
                                   value="<?php echo $value->iva; ?>">
                        </div>
                        <div id="columna2">
                            <input id="ieps[]<?php echo $key + 1000; ?>"
                                   type="text"
                                   name="ieps[]"
                                   class="ieps cantidades"
                                   value="<?php echo $value->ieps; ?>">
                        </div>
                        <div id="columna2">
                            <div id="total"></div>
                        </div>
                    </div>
                <?php
                }
            }
            ?>
            <div class="contenidos" id="contenidos">
                <div id="columna2">
                    <input type="text" name="producto[]" id="field" placeholder="Ingrese el nombre del producto" class="typeahead productos" data-items="3">
                </div>
                <div id="columna2"><input id="cantidad" type="text" name="cantidad[]" value="0" class="cantidad cantidades" ></div>
                <div id="columna2"><input id="descripcion" type="text" name="descripcion[]"></div>
                <div id="columna2"><input id="unidad" type="text" name="unidad[]" class="cantidades"></div>
                <div id="columna2"><input id="p_unitario" type="text" name="p_unitario[]" value="0" class="p_unit cantidades" ></div>
                <div id="columna2"><div id="subtotal"></div></div>
                <div id="columna2"><input id="iva" type="text" name="iva[]" value="0" class="iva cantidades"></div>
                <div id="columna2"><input id="ieps" type="text" name="ieps[]" value="0" class="ieps cantidades"></div>
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