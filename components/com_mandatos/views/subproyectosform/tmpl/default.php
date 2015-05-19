<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$proyectos 	= $this->data;
$proyecto 	= $this->proyecto;
$selected 	= '';
?>

<script src="libraries/integradora/js/tim-validation.js"> </script>
<script>
	function limpiar() {
		jQuery('input[name*="id_proyecto"]').val('<?php echo $proyecto->id_proyecto; ?>');
		jQuery('input[name*="integradoId"]').val('<?php echo $proyecto->integradoId; ?>');
		jQuery('input[name*="status"]').val('<?php echo $proyecto->status; ?>');
		jQuery('select[name*="parentId"]').val(1);
		jQuery('input[name*="name"]').val('');
		jQuery('textarea[name*="description"]').val('');
	}

	jQuery(document).ready(function(){
		jQuery('#form_alta').submit(function(event) {
			event.preventDefault();
			envioAjax();
		});
		jQuery('input:button').on('click', envioAjax);
		jQuery('#empty').on('click',limpiar);
	});

	function envioAjax() {
		var form = jQuery('#form_alta');
		var data = form.serialize();

		var request = jQuery.ajax({
			url: "index.php?option=com_mandatos&view=proyectosform&task=proyectosform.saveSubProject&format=raw",
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

	function cancelfunction(){
		window.history.back();
	}
</script>
<form id="form_alta" method="post" action="" autocomplete="off">
	<input type="hidden" name="id_proyecto" value="<?php echo $proyecto->id_proyecto; ?>" />
	<input type="hidden" name="integradoId" value="<?php echo $proyecto->integradoId; ?>" />
	<input type="hidden" name="status" value="<?php echo $proyecto->status; ?>" />

	<h1 style="margin-bottom: 40px;"><?php echo JText::_($this->titulo); ?></h1>
	
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
        <select name="parentId" id="parentId">
            <option value="0">Seleccione su proyecto</option>
			<?php
				foreach ($proyectos as $key => $value) {
					if( !is_null($proyecto) ){
						if( $proyecto->parentId == $value->id_proyecto  ){
							$selected = 'selected';
						}else{
							$selected = '';
						}
					}
					echo '<option value="'.$value->id_proyecto.'" '.$selected.' >'.$value->name.'</option>';
				}
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_SUBPROYECTO') ?></label>
		<input type="text" name="name" id="name" value="<?php echo $proyecto->name; ?>" />
	</div>
	
	<div class="form-group">
		<label for="description"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESCRIPCION_SUBP') ?></label>
		<textarea name="description" id="description" rows="10" style="width: 90%;" maxlength="1000"><?php echo $proyecto->description; ?></textarea>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary span3" id="send"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		<a class="btn btn-danger span3" id="cancel" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectoslist'); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
	</div>

	<?php if( is_null($proyecto->id_proyecto) ){ ?>
    <div class="form-actions">
        <button type="button" class="btn btn-default span3" id="empty"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    </div>
	<?php } ?>
</form>