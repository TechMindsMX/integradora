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
$attsCal2        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$attsCal3        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
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

    function showhide(id) {

        var up = jQuery('#dup'+id).val();
        var end = jQuery('#dend'+id).val();
        var fechas=jQuery('.'+id+'');
        console.log(fechas);

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
                  echo JHTML::_('calendar',$default, 'dupfecha', 'dupfecha', $format = '%Y-%m-%d', $attsCal);
                  ?>
              </div>
              <div>
                <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                <?php
                $default = date('Y-m-d');
                echo JHTML::_('calendar',$default, 'dendfecha', 'dendfecha', $format = '%Y-%m-%d', $attsCal);
                ?>
              </div>
                <div>
                    <button id="greporte" class="btn btn-primary span2" type="button">Generar Reporte</button>
                </div>
              <div style="margin: auto;">
                  <button id="fecha" onclick="showhide(this.id)" class="btn btn-primary span2" type="button">Buscar</button>
              </div>
            </div>
        </div>


            <div id="odv" class="table table-bordered" >
                <div class="contenidos" id="contenidos">
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
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'lr-eflujo', JText::_('COM_REPORTES_LR_EFLUO'));
    ?>
    <fieldset>
        <div>
            <div class="form-group" style="margin-left: 31px; text-align: 0;">
                <div style="display: inline-block">
                    <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
                    <?php
                    $default = date('Y-m-d');
                    echo JHTML::_('calendar',$default, 'dupfecha2', 'dupfecha2', $format = '%Y-%m-%d', $attsCal2);
                    ?>
                </div>
                <div>
                    <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                    <?php
                    $default = date('Y-m-d');
                    echo JHTML::_('calendar',$default, 'dendfecha2', 'dendfecha2', $format = '%Y-%m-%d', $attsCal2);
                    ?>
                </div>
                <div>
                    <button id="greporte" class="btn btn-primary span2" type="button">Generar Reporte  </button>
                </div>
                <div style="margin: auto;">
                    <button id="fecha2" onclick="showhide(this.id)" class="btn btn-primary span2" type="button">Buscar</button>
                </div>
            </div>
        </div>



    </fieldset>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'lr-eresul', JText::_('COM_REPORTES_LR_ERESUL'));
    ?>
    <fieldset>
        <div>

            <div class="form-group" style="margin-left: 31px; text-align: 0;">
                <div style="display: inline-block">
                    <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
                    <?php
                    $default = date('Y-m-d');
                    echo JHTML::_('calendar',$default, 'dupfecha3', 'dupfecha3', $format = '%Y-%m-%d', $attsCal3);
                    ?>
                </div>
                <div>
                    <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                    <?php
                    $default = date('Y-m-d');
                    echo JHTML::_('calendar',$default, 'dendfecha3', 'dendfecha3', $format = '%Y-%m-%d', $attsCal3);
                    ?>
                </div><div>
                    <button id="greporte" class="btn btn-primary span2" type="button">Generar Reporte</button>
                </div>
                <div style="margin: auto;">
                    <button id="fecha3" onclick="showhide(this.id)" class="btn btn-primary span2" type="button">Buscar</button>
                </div>

            </div>

        </div>

        </div>

    </fieldset>

    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.endTabSet');
    echo JHtml::_('form.token');
    ?>

</form>