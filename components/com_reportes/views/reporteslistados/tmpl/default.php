<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
echo '<script src="libraries/integradora/js/tim-validation.js"> </script>';
$attsCal        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$attsCal2        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$attsCal3        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$rfc                = $this->data->integrados[0]->datos_personales->rfc;
$integradoName      = $this->data->integrados[0]->datos_empresa->razon_social;
$calle              = $this->data->integrados[0]->datos_empresa->calle;
$no_ext             = $this->data->integrados[0]->datos_empresa->num_exterior;
$no_int             = $this->data->integrados[0]->datos_empresa->num_interior;
$cp                 = $this->data->integrados[0]->datos_empresa->cod_postal;
$direccion          = 'Calle; '.$calle.', No. Exterior '.$no_ext.', No. Interior '.' C.P. '.$cp;

$formToken  = JSession::getFormToken(true).'=1';

$url_flujo = 'index.php?option=com_reportes&view=flujo&'.$formToken;
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

    <?php
    echo '<h1>'.JText::_('COM_REPORTES_TITLE_LISTADOS').'</h1>';
    echo JHtml::_('bootstrap.startTabSet', 'tabs-lr', array('active' => 'balance'));
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'balance', JText::_('COM_REPORTES_LR_BALANCE'));
    ?>

<form action="" class="form" id="periodo" name="periodo" method="post" enctype="multipart/form-data" >
    <fieldset>
        <div>

	    <div class="form-group">
		    <a class="btn btn-success" href="<?php echo 'index.php?option=com_reportes&view=balance&id=&'.$formToken; ?>">Balance periodo actual</a>
	    </div>

    </fieldset>
</form>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'lr-eflujo', JText::_('COM_REPORTES_LR_EFLUO'));
    ?>
<form action="<?php echo $url_flujo; ?>" class="form" id="periodo" name="periodo" method="post" enctype="multipart/form-data" >
    <fieldset id="flujo">
        <div>
            <div class="form-group" style="margin-left: 31px; text-align: 0;">
                <div style="display: inline-block">
                    <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
                    <?php
                    $default = date('d-m-Y');
                    echo JHTML::_('calendar',$default, 'startDate', 'dupfecha2', $format = '%d-%m-%Y', $attsCal2);
                    ?>
                </div>
                <div>
                    <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                    <?php
                    $default = date('d-m-Y');
                    echo JHTML::_('calendar',$default, 'endDate','dendfecha2', $format = '%d-%m-%Y', $attsCal2);
                    ?>
                </div>
                <div>
                    <button id="greporte_flujo" class="btn btn-primary span2" type="submit">Generar Reporte  </button>
                </div>
                <div style="margin: auto;">
                    <button id="fecha2" onclick="showhide(this.id)" class="btn btn-primary span2" type="button">Buscar</button>
                </div>
            </div>
        </div>



    </fieldset>
</form>
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
                    $default = date('d-m-Y');
                    echo JHTML::_('calendar',$default, 'dupfecha3', 'dupfecha3', $format = '%d-%m-%Y', $attsCal3);
                    ?>
                </div>
                <div>
                    <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
                    <?php
                    $default = date('d-m-Y');
                    echo JHTML::_('calendar',$default, 'dendfecha3', 'dendfecha3', $format = '%d-%m-%Y', $attsCal3);
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