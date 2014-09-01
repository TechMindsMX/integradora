<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

function proyectos ($parent, $proys){
	echo "<ul class='listaproyectos'>";	
	foreach ($proys as $key => $value) {
			
		if($value->parentId == $parent){
				
			if($value->status == 1){
				$checked = 'checked';
				$disabled = 'disabled="disabled"';
			}else{
				$checked = '';
				$disabled = '';
			}
			
			echo '<li class="proyectoslist">'.
				 	'<div class="filas">'.
				 		'<div class="columnas"><span class="status'.$value->status.'">'.$value->name.'</span></div>'.
						 '<div class="columnas"><input type="button" '.$disabled.' class="btn btn-primary editar" id="'.$value->id.'" value="'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'" /></div>'.
						 '<div class="columnas"><input type="checkbox" class="deshabilitar" id="'.$value->id.'" '.$checked.' /></div>'.
				 	'</div>';
				
				proyectos($value->id, $proys);
		}else{
			echo '</li>';
		}
	}
	echo "</ul>";
}
$proyectos = $this->data;

if( !is_null($proyectos) ){
	$agregarSubproyecto = '<a class="btn btn-primary span3" href="'.JRoute::_('index.php?option=com_mandatos&view=altasubproyectos').'">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_AGREGAR_SUBPROYECTO').'</a>';
}else{
	JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PROJECTS'), 'Message');
	$agregarSubproyecto = '';
}
?>
<script>
	jQuery(document).ready(function(){
		jQuery('.deshabilitar').on('click', deshabilita);
		jQuery('.editar').on('click', editarProy);
	});
	
	function deshabilita(){
		var boton = jQuery(this);
		
		if(confirm('<?php echo JText::_('LAB_WARNING_DISABLED'); ?>')){
			if(!boton.prop('checked')){
				boton.parent().parent().find('.editar').prop('disabled', false);
			}else{
				boton.parent().parent().find('.editar').prop('disabled', true);
			}
		}else{
			console.log('no hago nada');
		}
	}
	
	function editarProy(){
		var id = jQuery(this).prop('id');
		
		window.location = 'index.php?option=com_mandatos&task=editarproyecto&proyId='+id;
	}
</script>
	<h1 style="margin-bottom: 40px;"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TITULO'); ?></h1>

	<a class="btn btn-primary span3" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=altaproyectos'); ?>">
		<?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_AGREGAR_PROYECTO'); ?>
	</a>
	<?php echo $agregarSubproyecto; ?>

	<div class="proyectos">
		<div class="filas" style="margin-bottom: 30px;">
			<div class="columnas"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO'); ?></div>
			<div class="columnas">&nbsp;</div>
			<div class="columnas"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESHABILITA_PROYECTO'); ?></div>
		</div>
		<?php !is_null($proyectos)?proyectos(0, $proyectos):''; ?>
	</div>

	<div style="margin-top: 20px;">
		<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>" />
			<?php echo JText::_('COM_MANDATOS_TITULO'); ?>
		</a>
	</div>