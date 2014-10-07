<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

?>
<form action="" class="form" id="altaC_P" name="altaC_P" method="post" enctype="multipart/form-data" >
<?php
echo JHtml::_('bootstrap.startTabSet', 'tabs-odv', array('active' => 'seleccion'));
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'seleccion', JText::_('COM_MANDATOS_ODV_SELECCION'));


?>

<script>
    var subprojects = <?php echo json_encode($this->proyectos['subproyectos']);?>;
    var global_var=0;
    var arrayProd = <?php echo json_encode($this->products)?>;
    var nextinput = 0;
    var pre="";

    jQuery(document).ready(function() {
        jQuery('#project').on('change', llenasubproject);

        jQuery.each(arrayProd, function (key, value) {
            jQuery('#productos').append('<option value="' + value.id + '">' + value.productName + '</option>');
        });

        jQuery('.productos').on('change', llenatabla);

        jQuery('#button').on('click', addrow);

        function addrow(){
            nextinput++;
            var tds = '<tr>';

                tds += '<td>' +
                        "<select id='productos"+nextinput+"' name='productos' class='productos'>";

                jQuery.each(arrayProd, function (key, value) {

                    tds+='<option value="' + value.id + '">' + value.productName + '</option>';
                });

                tds += '<td><input id="cantidad'+nextinput+'" type="text" name="cantidad" value=""></td>';
                tds += '<td><input id="descripcion'+nextinput+'" type="text" name="descripcion" value=""></td>';
                tds += '<td><input id="unidad'+nextinput+'" type="text" name="unidad" value=""></td>';
                tds += '<td><input id="p_unitario'+nextinput+'" type="text" name="p_unitario" value=""></td>';
                tds += '<td><input id="iva'+nextinput+'" type="text" name="iva" value=""></td>';
                tds += '<td><input id="ieps'+nextinput+'" type="text" name="ieps" value=""></td>';
                tds += '<td><div id="subtotal"></div></td>';
                tds += '<td><div id="total"></div> </td>';
            tds += '</tr>';
            jQuery("#odv").append(tds);

            jQuery('.productos').on('change', llenatabla);
        }

        function llenasubproject() {
            var select = jQuery(this).val();
            var selectSPro = jQuery('#subproject')
            var subprojectos = subprojects[select];

            jQuery.each(jQuery('#subproject').find('option'), function () {
                console.log(jQuery(this).remove());
            });

            selectSPro.append('<option value="0">Subproyecto</option>');

            jQuery.each(subprojectos, function (key, value) {
                selectSPro.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        }

        function llenatabla() {
            var idproducto  = jQuery(this).val();
            var trproductos = jQuery(this).parent().parent();
            var producto = '';

            jQuery.each(arrayProd, function (key, value) {
                if (idproducto == value.id) {
                    producto = value;
                }
            });

            trproductos.find('[name*="descripcion"]').val(producto.description);
            trproductos.find('[name*="unidad"]').val(producto.measure);
            trproductos.find('[name*="p_unitario"]').val(producto.price);
            trproductos.find('[name*="iva"]').val(producto.iva);
            trproductos.find('[name*="ieps"]').val(producto.ieps);
        }
    });

</script>
<fieldset>
    <select name="projectId" id="project">
        <option value="0">Proyecto</option>
        <?php
        foreach ($this->proyectos['proyectos'] as $key => $value) {
            echo '<option value="'.$value->id.'">'.$value->name.'</option>';
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
    <button type="button" class="btn btn-baja span3" id="tipoAlta"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    <button type="button" class="btn btn-primary span3" id="tipoAlta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    <button type="button" class="btn btn-danger span3" id="tipoAlta"><?php echo JText::_('LBL_CANCELAR'); ?></button>
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
    <table class="table table-bordered" id="odv">
        <thead>
        <tr>
            <th class="span2"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_TITULO'); ?></th>
            <th class="span4"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
            <th class="span1"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_UNIDAD'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
            <th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?></th>
            <th class="span1"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_TOTAL'); ?></th>
        </tr>
        </thead>
        <tbody>
            <tr class="trOdv">
                <td>
                    <select id='productos' name="productos" class="productos">
                        <option value="abierto">Abierto</option>
                    </select>
                </td>
                <td><input id="cantidad" type="text" name="cantidad" value=""></td>
                <td><input id="descripcion" type="text" name="descripcion" value=""></td>
                <td><input id="unidad" type="text" name="unidad" value=""></td>
                <td><input id="p_unitario" type="text" name="p_unitario" value=""></td>
                <td><input id="iva" type="text" name="iva" value=""></td>
                <td><input id="ieps" type="text" name="ieps" value=""></td>
                <td><div id="subtotal"></div></td>
                <td><div id="total"></div> </td>

            </tr>
        </tbody>
    </table>
<button type="button" id="button" name="button">+</button>
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="tipoAlta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'previewodv', JText::_('COM_MANDATOS_ODV_PREV'));
?>
<fieldset>
   pantalla 3
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>

<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'pers-juridica', JText::_('COM_MANDATOS_ODV_FACTURA'));
?>
<fieldset>
    pantalla 4
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'pers-juridica', JText::_('COM_MANDATOS_ODV_COMPROBANTE'));
?>
<fieldset>
    pantalla 5
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.endTabSet');
echo JHtml::_('form.token');
?>

</form>