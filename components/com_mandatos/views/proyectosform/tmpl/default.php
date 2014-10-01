<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$proyecto 	= isset($this->proyecto)?$this->proyecto:null;

?>
<script>
	jQuery(document).ready(function(){
		jQuery('#cancel').on('click', cancelfunction);
        jQuery('#send').on('click', confirmacion);
	});
	
	function cancelfunction(){
		window.history.back();
	}
    function confirmacion(){
        var formulario = jQuery('#form_alta');
        var confirmacion    = jQuery('#confirmacion');

        formulario.hide();
        confirmacion.show();
    }
</script>
<form id="form_alta" method="post" action="<?php echo JRoute::_('http://localhost/integradora/index.php?option=com_mandatos&view=proyectosform&integradoId='.$this->integradoId); ?>">
	<h1 style="margin-bottom: 40px;"><?php echo JText::_($this->titulo); ?></h1>
	
	<input type="hidden" name="token" value="<?php echo $this->token; ?>" />
    <input type="hidden" name="confirm" value="1" />

	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
		<input type="text" name="name" id="name" value="<?php echo !is_null($proyecto)?$proyecto->name:''; ?>">
	</div>
	
	<div class="form-group">
		<label for="description"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESCRIPCION_PROY') ?></label>
		<textarea name="description" id="description" rows="10" style="width: 90%;"><?php echo !is_null($proyecto)?$proyecto->description:''; ?></textarea>
	</div>
	
	<div class="form-actions">
		<button type="button" class="btn btn-primary span3" id="send"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <button type="button" class="btn btn-danger span3" id="cancel"><?php echo JText::_('LBL_CANCELAR'); ?></button>
		
	</div>
	
	<?php if( is_null($proyecto) ){ ?>
		<div class="form-actions">
			<button type="button" class="btn btn-primary span3" id="empty"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
		</div>
	<?php } ?>
</form>
<div id="confirmacion">
    <div></div>
</div>