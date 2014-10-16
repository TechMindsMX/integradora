<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';
$attsCal        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

$rfc                = $this->data->integrados[0]->datos_personales->rfc;
$integradoId        = $this->data->integrados[0]->datos_empresa->razon_social;
$calle              = $this->data->integrados[0]->datos_empresa->calle;
$no_ext             = $this->data->integrados[0]->datos_empresa->num_exterior;
$no_int             = $this->data->integrados[0]->datos_empresa->num_interior;
$cp                 = $this->data->integrados[0]->datos_empresa->cod_postal;
$direccion          = 'Calle; '.$calle.', No. Exterior '.$no_ext.', No. Interior '.' C.P. '.$cp;


?>
<script>

    function balance() {
        var up = jQuery('#dup').val();
        var end = jQuery('#dend').val();

        var valor = parseInt(jQuery(this).val());

        switch (valor) {
            case 0:
                jQuery('.type_0').show();
                jQuery('.type_1').hide();
                break;
            case 1:
                jQuery('.type_1').show();
                jQuery('.type_0').hide();
                break;
            case 3:
                jQuery('.type_0').show();
                jQuery('.type_1').show();
                break;

        }
    }
</script>

<form action="" class="form" id="periodo" name="periodo" method="post" enctype="multipart/form-data" >
    <?php
    echo '<h1>'.JText::_('COM_REPORTES_TITLE_LISTADOS').'</h1>';
    echo JHtml::_('bootstrap.startTabSet', 'tabs-lr', array('active' => 'balance'));
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'balance', JText::_('COM_REPORTES_LR_BALANCE'));
    ?>

    <fieldset>
        <div class="form-group" id="odv">
            <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
            <?php
            $default = date('Y-m-d');
            echo JHTML::_('calendar',$default, 'dup', 'dup', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div><div id="contenidos" class="form-group">
            <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
            <?php
            $default = date('Y-m-d');
            echo JHTML::_('calendar',$default, 'dend', 'dend', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div>

        <div><button id="buttonsearch" onclick="balance()" class="btn btn-primary span2" type="button">Buscar</button></div>
            <div id="odv" class="table table-bordered" >
                <div class="head" id="head">
                    <div id="columna1"><span class="etiqueta">Fecha</span></div>
                    <div id="columna1"><span class="etiqueta">Descripcion</span> </div>
                    <div id="columna1"><span class="etiqueta">Integrado</span></div>
                    <div id="columna1"><span class="etiqueta">RFC</span> </div>
                    <div id="columna1"><span class="etiqueta">Direccion</span> </div>


                </div>
                <?php foreach ($this->balances as $key => $value) {?>
                <div class="contenidos" id="contenidos">
                    <div id="columna1"><?php echo $value->created; ?></div>
                    <div id="columna1" style=" width: 58%;"><?php echo $value->observaciones ?> </div>
                    <div id="columna1"><?php echo $integradoId; ?></div>
                    <div id="columna1"><?php echo $rfc ?></div>
                    <div id="columna1"><?php echo $direccion ?></div>

                </div>
                <?php }?>
            </div>
    </fieldset>

    <div class="form-actions" style="max-width: 30%">
        <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
        <button type="button" class="btn btn-primary span3" id="seleccion"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
    </div>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'lr-eflujo', JText::_('COM_REPORTES_LR_EFLUO'));
    ?>
    <fieldset>
        estado de flujo
    </fieldset>
    <div class="form-actions" style="max-width: 30%">
        <button type="button" class="btn btn-baja span3" id="clear_form"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
        <button type="button" class="btn btn-primary span3" id="seleccion"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <button type="button" class="btn btn-danger span3" id="cancel_form"><?php echo JText::_('LBL_CANCELAR'); ?></button>
    </div>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'lr-eresul', JText::_('COM_REPORTES_LR_ERESUL'));
    ?>
    <fieldset>
        estado de resultados

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