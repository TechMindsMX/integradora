<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = $this->data;
$postUrl = $datos->action->post;

?>

<h2></h2>

<form action="" class="form" id="solicitud" name="solicitud" >
	<?php
		echo JHtml::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'pers-juridica'));
		echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_INTEG_PERS_JURIDICA'), 'pers-juridica');
	?>
	<fieldset>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral1" value="1" /><?php echo JText::_('COM_INTEG_SOLICITUD_PER_MORAL'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral2" value="2" /><?php echo JText::_('COM_INTEG_SOLICITUD_PER_FISICA'); ?></label>
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_INTEG_SOLICITUD_SLIDE_BASIC'), 'basic-details');
	?>
	<fieldset>
		<div class="form-group">
			<label for="apePat"><?php echo JText::_('APE_PAT'); ?></label>
			<input name="apePat" type="text" maxlength="50" value="<?php echo '';?>" disabled="disabled" />
		</div>
		<div class="form-group">
			<label for="apeMat"><?php echo JText::_('APE_MAT'); ?></label>
			<input name="apeMat" type="text" maxlength="50" />
		</div>
		<div class="form-group">
			<label for="nombre"><?php echo JText::_('LBL_NOMBRE'); ?></label>
			<input name="nombre" type="text" maxlength="50" />
		</div>
		<div class="form-group">
			<label for="nacionalidad"><?php echo JText::_('COM_INTEG_SOLICITUD_NACIONALIDAD'); ?></label>
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
			<label for="rfc"><?php echo JText::_('RFC'); ?></label>
			<input name="rfc" type="text" maxlength="18" />
		</div>
	</fieldset>	
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_INTEG_SOLICITUD_SLIDE_DIRECCION'), 'direccion');
	?>
	<fieldset>
		<div class="form-group">
           	<label for="doFi_nomCalle"><?php echo JText::_('LBL_CALLE'); ?> *:</label>
            <input 
            	name		="doFi_nomCalle" 
            	class		="validate[required,custom[onlyLetterNumber]]" 
            	type		="text" 
            	id			="doFi_nomCalle" 
            	maxlength	="70" />
        </div>
        
        <div class="_25">
           	<label for="doFi_noExterior"><?php echo JText::_('NUM_EXT'); ?>*:</label>
           	<input 
           		name		="doFi_noExterior" 
           		class		="validate[required,custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noExterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="_25">
           	<label for="doFi_noInterior"><?php echo JText::_('NUM_INT'); ?>:</label>
           	<input 
           		name		="doFi_noInterior" 
           		class		="validate[custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noInterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="_25">
           	<label for="doFi_iniCodigoPostal"><?php echo JText::_('LBL_CP'); ?> *:</label>
           	<input 
           		name		="doFi_perfil_codigoPostal_idcodigoPostal" 
           		class		="validate[required,custom[onlyNumberSp], minSize[5]]"  
           		type		="text" 
           		id			="doFi_iniCodigoPostal" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="_75">
           	<label for="doFi_nomColonias"><?php echo JText::_('LBL_COLONIA'); ?> *:</label>
           	<select name="doFi_perfil_colonias_idcolonias" class="validate[required]" id="doFi_nomColonias"></select>
        </div>
        
        <div class="_50">
        	<label for="doFi_nomDelegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *:</label>
        	<select name="doFi_perfil_delegacion_iddelegacion" class="validate[required,custom[onlyLetterSp]]" id="doFi_nomDelegacion"></select>
        </div> 
        
        <div class="_25">
           	<label for="doFi_nomEstado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
           	<select name="doFi_perfil_estado_idestado" id="doFi_nomEstado" class="validate[required]" ></select>
        </div>
        
        <div class="_25">
           	<label for="doFi_nomPais"><?php echo JText::_('LBL_PAIS'); ?> *:</label>
           	<select name="doFi_perfil_pais_idpais" id="doFi_nomPais" class="validate[required]">
           		<option value="1" selected="selected">M&eacute;xico</option>
			</select>
		</div>
		
	</fieldset>

	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-contact', JText::_('COM_INTEG_SOLICITUD_SLIDE_EXTRAS'), 'ext-details');
	?>
	<fieldset>
		<div class="form-group">
			<label for="telfijo"><?php echo JText::_('COM_INTEG_SOLICITUD_TEL_FIJO'); ?></label>
			<input name="telfijo" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="ext"><?php echo JText::_('COM_INTEG_SOLICITUD_EXT'); ?></label>
			<input name="ext" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="telmovil"><?php echo JText::_('COM_INTEG_SOLICITUD_TEL_MOVIL'); ?></label>
			<input name="telmovil" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="email"><?php echo JText::_('LBL_CORREO'); ?></label>
			<input name="email" type="email" maxlength="" />
		</div>
		<div class="form-group">
			<label for="nomComercial"><?php echo JText::_('COM_INTEG_SOLICITUD_NOM_COMERCIAL'); ?></label>
			<input name="nomComercial" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="identificacion-file"><?php echo JText::_('COM_INTEG_SOLICITUD_ID_FILE'); ?></label>
			<input name="identificacion-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="rfc-file"><?php echo JText::_('COM_INTEG_SOLICITUD_RFC_FILE'); ?></label>
			<input name="rfc-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="curp"><?php echo JText::_('COM_INTEG_SOLICITUD_CURP'); ?></label>
			<input name="curp" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="curp-file"><?php echo JText::_('COM_INTEG_SOLICITUD_CURP_FILE'); ?></label>
			<input name="curp-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="comp-domicilio-file"><?php echo JText::_('COM_INTEG_SOLICITUD_COMP_DOMICILIO_FILE'); ?></label>
			<input name="comp-domicilio-file" type="file" maxlength="" />
		</div>
		
	</fieldset>

	<?php
		echo JHtml::_('bootstrap.endSlide');
	?>


	
</form>
<?php

// var_dump($this);
?>