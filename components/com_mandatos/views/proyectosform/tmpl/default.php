<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$proyecto = $this->proyecto;
?>
<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>
<script>
	function limpiar() {
		jQuery('input[name*="confirm"]').val(1);
		jQuery('input[name*="integradoId"]').val('<?php echo $proyecto->integradoId; ?>');
		jQuery('input[name*="status"]').val('<?php echo $proyecto->status ?>');
		jQuery('input[name*="parentId"]').val('<?php echo $proyecto->parentId ?>');
		jQuery('input[name*="id_proyecto"]').val('<?php echo $proyecto->id_proyecto ?>');
		jQuery('input[name*="name"]').val('');
		jQuery('textarea[name*="description"]').val('');
	}
	jQuery(document).ready(function(){
		jQuery('#form_alta').submit(function(event) {
			event.preventDefault();
			envioAjax();
		});
		jQuery('input:button').on('click', envioAjax);
		jQuery('#btn-limpiar').on('click', limpiar);
	});

	function envioAjax() {
		var form = jQuery('#form_alta');
		var data = form.serialize();

		var request = jQuery.ajax({
			url: "index.php?option=com_mandatos&view=proyectosform&task=proyectosform.saveProject&format=raw",
			data: data,
			type: 'post',
			async: false
		});

		request.done(function (result) {
			var envio = mensajesValidaciones(result);

			if(envio === true){
				document.location.href=result.redirect;
			}
		});
	}
</script>

<form id="form_alta" method="post" action="">
	<h1 style="margin-bottom: 40px;"><?php echo JText::_($this->titulo); ?></h1>

	<input type="hidden" name="confirm" 	value="1" />
	<input type="hidden" name="integradoId" value="<?php echo $proyecto->integradoId; ?>">
	<input type="hidden" name="parentId" 	value="<?php echo $proyecto->parentId ?>">
	<input type="hidden" name="id_proyecto" value="<?php echo $proyecto->id_proyecto ?>">

	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
		<input type="text" name="name" id="name" maxlength="100" value="<?php echo $proyecto->name ?>">
	</div>

	<div class="form-group">
		<label for="description"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESCRIPCION_PROY') ?></label>
		<textarea name="description" id="description" rows="10" maxlength="1000" style="width: 90%;"><?php echo $proyecto->description ?></textarea>
	</div>

	<div class="form-group">
		<label for="status"><?php echo JText::_('JSTATUS'); ?></label>
		<select class="form-control" name="status">
			<?php
			foreach ( $this->catalogos->basic as $value => $name ) {
				$selected = ($value == $proyecto->status) ? 'selected' : '';
				echo '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
			}
			?>
		</select>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary span3" id="send"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		<a class="btn btn-danger span3" id="cancel" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectoslist'); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>

	</div>
	<?php if(is_null($proyecto->id_proyecto )){?>
		<div class="form-actions">
			<button type="button" class="btn btn-default span3" id="btn-limpiar"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
		</div>
	<?php } ?>
</form>