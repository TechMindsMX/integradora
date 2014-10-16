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

   /* if((Date.parse(fech1)) > (Date.parse(fech2))){
        alert(‘La fecha inicial no puede ser mayor que la fecha final’);
    }
    }
*/

    function balance() {
        var up = jQuery('#dup').val();
        var end = jQuery('#dend').val();
        var fechas=jQuery('.fecha');




        jQuery.each(fechas, function (key, value) {
            var parent=jQuery(this).parent();
            var fecha=jQuery(this).html();
            var elem = fecha.split('-');
            var fech = elem[2]+'-'+elem[1]+'-'+elem[0];
            parent.hide();
            if((Date.parse(fech)) > (Date.parse(up))){
             if((Date.parse(fech)) < (Date.parse(end))){
                 parent.show();
             }

            }




        });
    }
</script>

<form action="" class="form" id="periodo" name="periodo" method="post" enctype="multipart/form-data" >
    <?php
    echo '<h1>'.JText::_('COM_REPORTES_TITLE_LISTADOS').'</h1>';
    echo JHtml::_('bootstrap.startTabSet', 'tabs-lr', array('active' => 'balance'));
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'balance', JText::_('COM_REPORTES_LR_BALANCE'));
    ?>

    <fieldset>
        <div>
            <div class="form-group" style="margin-left: 31px; text-align: 0;">
              <div style="display: inline-block">
                  <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
                  <?php
                  $default = date('Y-m-d');
                  echo JHTML::_('calendar',$default, 'dup', 'dup', $format = '%Y-%m-%d', $attsCal);
                  ?>
              </div>
              <div>
                <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                <?php
                $default = date('Y-m-d');
                echo JHTML::_('calendar',$default, 'dend', 'dend', $format = '%Y-%m-%d', $attsCal);
                ?>
              </div>
              <div style="margin: auto;">
                  <button id="buttonsearch" onclick="balance()" class="btn btn-primary span3" type="button">Buscar</button>
              </div>
            </div>
        </div>


            <div id="odv" class="table table-bordered" >
                <div class="head" id="head">
                    <div id="columna1"><span class="etiqueta">Fecha</span></div>
                    <div id="columna1"><span class="etiqueta">Descripcion</span> </div>
                    <div id="columna1"><span class="etiqueta">Integrado</span></div>
                    <div id="columna1"><span class="etiqueta">RFC</span> </div>
                    <div id="columna1"><span class="etiqueta">Direccion</span> </div>
                    <div id="columna1"><span class="etiqueta">Ver Balance</span></div>


                </div>
                <?php foreach ($this->balances as $key => $value) {?>
                <div class="contenidos" id="contenidos">
                    <div id="columna1" class="fecha"><?php echo $value->created; ?></div>
                    <div id="columna1" style=" width: 50%;"><?php echo $value->observaciones ?> </div>
                    <div id="columna1"><?php echo $integradoId; ?></div>
                    <div id="columna1"><?php echo $rfc ?></div>
                    <div id="columna1"><?php echo $direccion ?></div>
                    <div id="columna1" style="width: 50px">
                        <button type="button" class="btn btn-primary" id="clear_form"><?php echo JText::_('LBL_DBALANCE'); ?></button>
                    </div>


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