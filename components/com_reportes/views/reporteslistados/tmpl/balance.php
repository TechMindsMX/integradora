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

		jQuery('.monthYearPicker').datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'MM',
			minDate: '01 YY',
			maxDate: '-0M'
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
</script>

    <?php
    echo '<h1>'.JText::_('LBL_BALANCE').'</h1>';
    echo JHtml::_('bootstrap.startTabSet', 'tabs-lr', array('active' => 'balance'));
    echo JHtml::_('bootstrap.addTab', 'tabs-lr', 'balance', JText::_('COM_REPORTES_LR_BALANCE'));
    ?>

<form action="index.php?option=com_reportes&view=balance" class="form" id="resultados" name="resultados" method="post" enctype="multipart/form-data" >
	<fieldset>
		<div>

			<div class="form-group" style="margin-left: 31px;">
				<div>
					<label for="created"><?php echo JText::_('LBL_DUP'); ?></label>
					<input id="startDate" class="monthYearPicker" type="text" readonly />
				</div>
				<div>
					<button id="greporte" class="btn btn-primary span2" type="submit" disabled><?php echo JText::_('LBL_GENERAR_REPORTE'); ?></button>
					<a class="btn btn-danger" href="<?php echo 'index.php?option=com_reportes&view=reporteslistados'; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
				</div>

			</div>

		</div>

	</fieldset>

	<?php
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.endTabSet');
	?>
</form>

<?php
echo JHtml::_('bootstrap.endTab');
?>
