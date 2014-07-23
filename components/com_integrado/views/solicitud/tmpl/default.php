<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = $this->data;
$postUrl = $datos->action->post;

?>

<h2></h2>

<form action="" class="form" id="solicitud" name="solicitud" >
	<fieldset>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral1" value="1" /><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral2" value="2" /><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
		</div>
	</fieldset>

	<fieldset>
		<div class="form-group">
			<label for="apePat"><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
			<input name="apePat" type="text" maxlength="50" value="<?php echo '';?>" disabled="disabled" />
		</div>
		<div class="form-group">
			<label for="apeMat"><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
			<input name="apeMat" type="text" maxlength="50" />
		</div>
		<div class="form-group">
			<label for="nombre"><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
			<input name="nombre" type="text" maxlength="50" />
		</div>
		<div class="form-group">
			<select name="nacionalidad">
				<?php 
				foreach ($this->catalogos->nacionalidad as $key => $value) {
					$default = ($value->nombre == 'México') ? 'selected' : '';
					echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<select name="sexo">
				<option value="masculino"><?php echo JText::_('SEXO_MASCULINO'); ?></option>
				<option value="femenino"><?php echo JText::_('SEXO_FEMENINO'); ?></option>
			</select>
		</div>
		<div class="form-group">
			<?php 
			$atts = array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
			echo JHTML::_('calendar',date('d-m-Y'),'fechaNacimiento', 'fechaNacimiento', $format = '%d-%m-%Y', $atts);
			?>
		</div>
		<div class="form-group">
			<label for="rfc"><?php echo JText::_('COM_INTEGRADO_SOLICITUD_'); ?></label>
			<input name="rfc" type="text" maxlength="18" />
		</div>


	</fieldset>	
	
</form>
<?php

var_dump($this);
?>