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
		jQuery('.datepicker').datepicker();
	});

</script>

    <?php
    echo '<h1>'.JText::_('COM_REPORTES_TITLE_LISTADOS').'</h1>';
    echo JHtml::_('bootstrap.startTabSet', 'tabs-lr', array('active' => 'lr-eflujo'));

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
	                <a class="btn btn-danger" href="<?php echo 'index.php?option=com_reportes&view=reporteslistados'; ?>"><?php echo JText::_('JCANCEL'); ?></a>
                </div>
            </div>
        </div>

    </fieldset>
</form>
<?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.endTabSet');
    ?>
</form>

