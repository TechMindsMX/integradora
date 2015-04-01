<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$formToken  = JSession::getFormToken(true).'=1';

$projects = $this->projects;

$url_flujo = 'index.php?option=com_reportes&view=flujo&'.$formToken;
$url_resultados = 'index.php?option=com_reportes&view=resultados&'.$formToken;
?>

<script src="libraries/integradora/js/tim-validation.js"> </script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
<script src="libraries/integradora/js/tim-datepicker-defaults.js"> </script>

<script>
	jQuery(document).ready(function(){
		jQuery('#changePeriod').on('click',cambiarPeriodo);
		jQuery('.datepicker').datepicker();

		jQuery('.monthYearPicker').datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'MM yy',
			maxDate: '-1M'
		}).focus(function() {
			var thisCalendar = jQuery(this);
			jQuery('.ui-datepicker-calendar').detach();
			jQuery('.ui-datepicker-close').click(function() {
				var month = jQuery("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = jQuery("#ui-datepicker-div .ui-datepicker-year :selected").val();
				thisCalendar.datepicker('setDate', new Date(year, month, 1));
			});
		});

	});

	function cambiarPeriodo() {
		fechaInicial   = jQuery('#startDate').val();
		fechaFinal     = jQuery('#endDate').val();

		window.location = 'index.php?option=com_reportes&view=resultados&startDate='+fechaInicial+'&endDate='+fechaFinal;
	}

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
            <div class="form-group" style="margin-left: 31px;">
                <div style="display: inline-block">
                    <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
	                <input class="datepicker" id="startDate" name="startDate" type="text" readonly />
                </div>
	            <div>
		            <label for="created"><?php echo JText::_('LBL_DEND'); ?></label>
		            <input class="datepicker" id="endDate" name="endDate" type="text" readonly />
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
<form action="<?php echo $url_resultados; ?>" class="form" id="resultados" name="resultados" method="post" enctype="multipart/form-data" >
    <fieldset>
        <div>

            <div class="form-group" style="margin-left: 31px;">
                <div>
                    <label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
	                <input id="startDate" class="monthYearPicker" type="text" readonly />
                </div>
	            <div>
		            <label for="project"><?php echo JText::_('LBL_PROY');?></label>
		            <select name="project" id="project">
			            <option value=""><?php echo JText::_('LBL_SELECCIONE_OPCION');?></option>
			            <?php
			            foreach ( $projects as $project ) {
				            echo '<option value="'.$project->id_proyecto.'">'.$project->name.'</option>';
		                }
			            ?>
		            </select>
	            </div>
	            <div>
                    <button id="greporte" class="btn btn-primary span2" type="submit">Generar Reporte</button>
                </div>

            </div>

        </div>

    </fieldset>

    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.endTabSet');
    ?>
</form>

