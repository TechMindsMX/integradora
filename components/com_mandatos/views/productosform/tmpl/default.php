<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.css');
JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.min.css');

$producto 	= $this->producto;
//index.php?option=com_mandatos&task=saveProducts
?>
<script>
    function valida() {
        var valorCampo = jQuery('#formProduct').serialize();

        var request = jQuery.ajax({
            url: "index.php?option=com_mandatos&task=productosform.valida&format=raw",
            data:  valorCampo,
            type: 'post',
            async: false
        });

        request.done(function(response){
            var envio = mensajesValidaciones(response);

            if(envio === true){
                var formulario = jQuery('#formProduct');
                var selects = formulario.find('select');
                var textarea = formulario.find('textarea');
                var inputs = formulario.find('input:text');

                jQuery('#valor_description').html('<strong>'+textarea.val()+'</strong>');

                jQuery.each(selects,function(key, value){
                    var campo     = jQuery(value);
                    var nameCampo = campo.prop('name');
                    var selected  = campo.find('option:selected');

                    jQuery('#valor_'+nameCampo).html('<strong>'+selected.html()+'</strong>');
                });

                jQuery.each(inputs, function(k,value){
                    var nameCampo = jQuery(value).prop('name');
                    var valorCampo = jQuery(value).val();

                    jQuery('#valor_'+nameCampo).html('<strong>'+valorCampo+'</strong>')
                });

                jQuery('#formulario').hide();
                jQuery('#confirmacion').show();

            }
        });
    }
    function cancelconfirm() {
        jQuery('#formulario').show();
        jQuery('#confirmacion').hide();
    }

    jQuery(document).ready(function(){
        jQuery('#cancel').on('click', cancelfunction);
        jQuery('#enviar').on('click', valida);
        jQuery('#cancelarConfirm').on('click',cancelconfirm);
    });

    function cancelfunction(){
        window.location = 'index.php?option=com_mandatos&view=productosList';
    }
</script>
<script src="libraries/integradora/js/tim-validation.js"> </script>

<form id="formProduct" class="form col-lg-6 col-md-12" role="form" method="post" action="index.php?option=com_mandatos&task=productosform.saveProducts&format=raw" autocomplete="off">
    <div id="formulario">
        <h1><?php echo ucwords(JText::_($this->titulo)); ?></h1>

        <input type="hidden" id="integradoId" name="integradoId" value="<?php echo $producto->integradoId; ?>">
        <input type="hidden" id="id_producto" name="id_producto" value="<?php echo $producto->id_producto; ?>">
        <input type="hidden" id="status" name="status" value="<?php echo $producto->status; ?>">

        <div class="row">
            <div class="col-md-6">
                <label for="productName"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?></label>
                <input type="text"
                       class="alto form-control"
                       id="productName"
                       name="productName"
                       maxlength="100"
                       value="<?php echo $producto->productName; ?>"
                       placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME') ?>">
            </div>

            <div class="col-md-6">
                <label for="currency"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?></label>
                <select name="currency"
                        class="form-control"
                        id="currency">
                    <option value=""><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS'); ?></option>
                    <?php
                    foreach ($this->currencies as $key => $value) {
                        $selected = $value->code == $producto->currency?'selected = "selected"':'';
                        echo '<option value="'.$value->code.'"'.$selected.'>'.$value->code.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <label for="price"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?>: </label>
                <input type="text"
                       class="alto form-control"
                       id="price"
                       name="price"
                       maxlength="10"
                       value="<?php echo $producto->price; ?>"
                       placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO') ?>" />
            </div>
            <div class="col-md-6">
                <label for="iva"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>: </label>
                <select class="alto form-control" id="iva" name="iva">
                    <option><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS').' de '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?></option>
                    <?php
                    foreach ($this->catalogoIva as $key => $value) {
                        $selected = '';
                        if($producto->iva == $key){
                            $selected = 'selected="selected"';
                        }elseif($key == '3' && $producto->iva == ''){
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="'.$key.'" '.$selected.'>'.$value->leyenda.'</option>';
                    }
                    ?>
                </select>

            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <label for="measure"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?>: </label>
                <input type="text" class="alto form-control" name="measure" id="measure" value="<?php echo $producto->measure; ?>">
            </div>
            <div class="col-md-6">
                <label for="ieps"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>: </label>
                <input type="text"
                       class="alto form-control"
                       id="ieps"
                       name="ieps"
                       value="<?php echo $producto->ieps; ?>"
                       maxlength="4"
                       placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS') ?>"/>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <label for="description"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>: </label>
            <textarea name="description"
                      id="description"
                      rows="7"
                      style="width: 304px;"
                      maxlength="100"
                      class="form-control"
                      placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>"><?php echo $producto->description;?></textarea>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <button type="button" id="enviar" class="btn btn-primary"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <a href="index.php?option=com_mandatos&view=productoslist" class="btn btn-danger"><?php echo JText::_('LBL_CANCELAR'); ?></a>
    </div>

    <div id="confirmacion" style="display: none;">
        <h1><?php echo JText::_('LBL_COMFIRM_DATA'); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?>: </span><span id="valor_productName"></span>

            </div>

            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?>: </span><span id="valor_currency"></span><span id="valor_currency"></span>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?>: </span><span id="valor_price"></span>
            </div>
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>: </span><span id="valor_iva"></span>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?>: </span><span id="valor_measure"></span>
            </div>
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>: </span><span id="valor_ieps"></span>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <span><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>: </span><span id="valor_description"></span>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <button type="submit" class="btn btn-primary"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <button type="button" id="cancelarConfirm" class="btn btn-danger" id="cancel"><?php echo JText::_('LBL_CANCELAR'); ?></button>
    </div>
</form>

