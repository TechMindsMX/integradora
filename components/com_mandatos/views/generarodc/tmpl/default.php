<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
?>
<script>
	jQuery(document).ready(function(){
		jQuery('#proveedor').on('change', muestraboton);
		jQuery('#agregarProveedor').on('click', agregaProveedor);
	});
	
	function muestraboton(){
		var valorCampo = jQuery(this).val();
		console.log(valorCampo);
		
		if(valorCampo == 'other'){
			jQuery('#agregarProveedor').show();
		}else{
			jQuery('#agregarProveedor').hide();
		}
	}
	
	function agregaProveedor(){
		console.log(this);
	}
</script>

<h1><?php echo JText::_('COM_MANDATOS_ODC_FORM_TITULO'); ?></h1>

<form id="generaODC" method="post" action="#" role="form">
	<div class="form-group">
		<label for="proyecto"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
		<select id="proyecto">
			<?php
			foreach ($this->proyectos as $key => $value) {
				echo '<option value="'.$value->id.'">'.$value->name.'</option>';
			} 
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="proveedor"><?php echo JText::_('LBL_PROVEEDOR') ?></label>
		<select id="proveedor">
			<?php
			foreach ($this->proveedores as $key => $value) {
				echo '<option value="'.$value->id.'">'.$value->tradeName.'</option>';
			} 
			?>
			<option value="other"><?php echo JText::_('LBL_OTHER'); ?></option>
		</select>
		
		<div class="form-group" id="agregarProveedor" style="display: none;">
			<input type="button" class="btn btn-primary" value="<?php echo JText::_('LBL_CARGAR') ?>" />
		</div>
	</div>
	
	<div class="form-group">
		<label for="created"><?php echo JText::_('LBL_FECHA_NACIMIENTO'); ?></label>
		<?php 
		$default = date('Y-m-d');
		echo JHTML::_('calendar',$default,'created', 'created', $format = '%Y-%m-%d', $attsCal);
		?>
	</div>
</form>