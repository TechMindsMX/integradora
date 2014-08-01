<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = $this->data;
$postUrl = $datos->action->post;

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

?>

<form action="" class="form" id="solicitud" name="solicitud" >
	<input type="hidden" name="integrado_Id" value="<?php echo $datos->userId; ?>" />
	<?php
		echo JHtml::_('bootstrap.startTabSet', 'tabs-solicitud', array('active' => 'pers-juridica'));
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'pers-juridica', JText::_('COM_INTEG_PERS_JURIDICA'));
	?>
	<fieldset>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral1" value="1" /><?php echo JText::_('LBL_PER_MORAL'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="perFisicaMoral" id="perFisicaMoral2" value="2" /><?php echo JText::_('LBL_PER_FISICA'); ?></label>
		</div>
	</fieldset>
	
	<div class="form-actions">
		<button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
	</div>

	<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'basic-details', JText::_('LBL_SLIDE_BASIC'));
		echo JHtml::_('bootstrap.startAccordion', 'slide-basic', array('active' => 'basic-persona'));
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_BASIC'), 'basic-persona');
	?>
	<fieldset>
		<div class="form-group">
			<label for="apePat"><?php echo JText::_('APE_PAT'); ?></label>
			<input name="apePat" type="text" maxlength="50" value="<?php echo '';?>" />
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
			<label for="nacionalidad"><?php echo JText::_('LBL_NACIONALIDAD'); ?></label>
			<select name="nacionalidad">
				<?php 
				foreach ($this->catalogos->nacionalidades as $key => $value) {
					$default = ($value->nombre == 'México') ? 'selected' : '';
					echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="sexo"><?php echo JText::_('LBL_SEXO'); ?></label>
			<select name="sexo">
				<option value="masculino"><?php echo JText::_('SEXO_MASCULINO'); ?></option>
				<option value="femenino"><?php echo JText::_('SEXO_FEMENINO'); ?></option>
			</select>
		</div>
		<div class="form-group">
			<label for="fechaNacimiento"><?php echo JText::_('LBL_FECHA_NACIMIENTO'); ?></label>
			<?php 
			echo JHTML::_('calendar',date('d-m-Y'),'fechaNacimiento', 'fechaNacimiento', $format = '%d-%m-%Y', $attsCal);
			?>
		</div>
		<div class="form-group">
			<label for="rfc"><?php echo JText::_('LBL_RFC'); ?></label>
			<input name="rfc" type="text" maxlength="18" />
		</div>
	</fieldset>	
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_DIRECCION'), 'basic-direccion');
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
        
        <div class="form-group">
           	<label for="doFi_noExterior"><?php echo JText::_('NUM_EXT'); ?>*:</label>
           	<input 
           		name		="doFi_noExterior" 
           		class		="validate[required,custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noExterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_noInterior"><?php echo JText::_('NUM_INT'); ?>:</label>
           	<input 
           		name		="doFi_noInterior" 
           		class		="validate[custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noInterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_iniCodigoPostal"><?php echo JText::_('LBL_CP'); ?> *:</label>
           	<input 
           		name		="doFi_perfil_codigoPostal_idcodigoPostal" 
           		class		="validate[required,custom[onlyNumberSp], minSize[5]]"  
           		type		="text" 
           		id			="doFi_iniCodigoPostal" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_nomColonias"><?php echo JText::_('LBL_COLONIA'); ?> *:</label>
           	<select name="doFi_perfil_colonias_idcolonias"id="doFi_nomColonias"></select>
        </div>
        
        <div class="form-group">
        	<label for="doFi_nomDelegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *:</label>
        	<select name="doFi_perfil_delegacion_iddelegacion" id="doFi_nomDelegacion"></select>
        </div> 
        
        <div class="form-group">
           	<label for="doFi_nomEstado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
           	<select name="doFi_perfil_estado_idestado" id="doFi_nomEstado"></select>
        </div>
        
        <div class="form-group">
           	<label for="doFi_nomPais"><?php echo JText::_('LBL_PAIS'); ?> *:</label>
           	<select name="doFi_perfil_pais_idpais" id="doFi_nomPais" >
           		<option value="1" selected="selected">M&eacute;xico</option>
			</select>
		</div>
		
	</fieldset>

	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_EXTRAS'), 'ext-details');
	?>
	<fieldset>
		<div class="form-group">
			<label for="telfijo"><?php echo JText::_('LBL_TEL_FIJO'); ?></label>
			<input name="telfijo" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="ext"><?php echo JText::_('LBL_EXT'); ?></label>
			<input name="ext" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="telmovil"><?php echo JText::_('LBL_TEL_MOVIL'); ?></label>
			<input name="telmovil" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="email"><?php echo JText::_('LBL_CORREO'); ?></label>
			<input name="email" type="email" maxlength="" />
		</div>
		<div class="form-group">
			<label for="nomComercial"><?php echo JText::_('LBL_NOM_COMERCIAL'); ?></label>
			<input name="nomComercial" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="identificacion-file"><?php echo JText::_('LBL_ID_FILE'); ?></label>
			<input name="identificacion-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="rfc-file"><?php echo JText::_('LBL_RFC_FILE'); ?></label>
			<input name="rfc-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="curp"><?php echo JText::_('LBL_CURP'); ?></label>
			<input name="curp" type="text" maxlength="" />
		</div>
		<div class="form-group">
			<label for="curp-file"><?php echo JText::_('LBL_CURP_FILE'); ?></label>
			<input name="curp-file" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="comp-domicilio-file"><?php echo JText::_('LBL_COMP_DOMICILIO_FILE'); ?></label>
			<input name="comp-domicilio-file" type="file" maxlength="" />
		</div>
		
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.endAccordion');
	?>
		<div class="form-actions">
			<button type="button" class="btn btn-primary span3" id="personales"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
	
	<?php
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'empresa', JText::_('LBL_TAB_EMPRESA'));
		echo JHtml::_('bootstrap.startAccordion', 'empresa', array('active' => 'empresa-nombre'));
		echo JHtml::_('bootstrap.addSlide', 'empresa', JText::_('LBL_SLIDE_GENERALES'), 'empresa-nombre');
	?>
	<fieldset>
		<div class="form-group">
			<label for="razon-social"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?></label>
			<input name="razon-social" type="text" maxlength="100" />
		</div>
		<div class="form-group">
			<label for="rfc-empresa"><?php echo JText::_('LBL_RFC'); ?></label>
			<input name="rfc-empresa" type="text" maxlength="18" />
		</div>
		<div class="form-group">
			<label for="rfc-empresa-file"><?php echo JText::_('LBL_RFC_FILE'); ?></label>
			<input name="rfc-empresa-file" type="file" maxlength="" />
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'empresa', JText::_('LBL_SLIDE_DIRECCION'), 'empresa-direccion');
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
        
        <div class="form-group">
           	<label for="doFi_noExterior"><?php echo JText::_('NUM_EXT'); ?>*:</label>
           	<input 
           		name		="doFi_noExterior" 
           		class		="validate[required,custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noExterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_noInterior"><?php echo JText::_('NUM_INT'); ?>:</label>
           	<input 
           		name		="doFi_noInterior" 
           		class		="validate[custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="doFi_noInterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_iniCodigoPostal"><?php echo JText::_('LBL_CP'); ?> *:</label>
           	<input 
           		name		="doFi_perfil_codigoPostal_idcodigoPostal" 
           		class		="validate[required,custom[onlyNumberSp], minSize[5]]"  
           		type		="text" 
           		id			="doFi_iniCodigoPostal" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="doFi_nomColonias"><?php echo JText::_('LBL_COLONIA'); ?> *:</label>
           	<select name="doFi_perfil_colonias_idcolonias"id="doFi_nomColonias"></select>
        </div>
        
        <div class="form-group">
        	<label for="doFi_nomDelegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *:</label>
        	<select name="doFi_perfil_delegacion_iddelegacion" id="doFi_nomDelegacion"></select>
        </div> 
        
        <div class="form-group">
           	<label for="doFi_nomEstado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
           	<select name="doFi_perfil_estado_idestado" id="doFi_nomEstado"></select>
        </div>
        
        <div class="form-group">
           	<label for="doFi_nomPais"><?php echo JText::_('LBL_PAIS'); ?> *:</label>
           	<select name="doFi_perfil_pais_idpais" id="doFi_nomPais" >
           		<option value="1" selected="selected">M&eacute;xico</option>
			</select>
		</div>
		<div class="form-group">
			<label for="comp-domicilio-empresa-file"><?php echo JText::_('LBL_COMP_DOMICILIO_FILE'); ?></label>
			<input name="comp-domicilio-empresa-file" type="file" maxlength="" />
		</div>
		
	</fieldset>

	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'empresa', JText::_('LBL_SLIDE_TESTIMONIOS'), 'empresa-testimonios');
	?>
	<fieldset>
		<div id="testimonio1">
			<h3><?php echo JText::_('LBL_TESTIMONIO1'); ?></h3>
			<div class="form-group">
				<label for="testimonio1-fecha-const"><?php echo JText::_('LBL_FECHA_CONSTITUCION'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('d-m-Y'),'testimonio1-fecha-const', 'testimonio1-fecha-const', $format = '%d-%m-%Y', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="testimonio1-notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="testimonio1-notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="doFi_nomEstado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="testimonio1-estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="testimonio1-notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="testimonio1-notario" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="testimonio1-numero"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="testimonio1-numero" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="testimonio1-file"><?php echo JText::_('LBL_TESTIMONIO1_FILE'); ?></label>
				<input name="testimonio1-file" type="file" maxlength="" />
			</div>
		</div>

		<div id="testimonio2">
			<h3><?php echo JText::_('LBL_TESTIMONIO2'); ?></h3>
			<div class="form-group">
				<label for="testimonio2-fecha-const"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('d-m-Y'),'testimonio2-fecha-const', 'testimonio2-fecha-const', $format = '%d-%m-%Y', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="testimonio2-notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="testimonio2-notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="testimonio2-estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="testimonio2-estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="testimonio2-notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="testimonio2-notario" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="testimonio2-numero"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="testimonio2-numero" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="testimonio2-file"><?php echo JText::_('LBL_TESTIMONIO2_FILE'); ?></label>
				<input name="testimonio2-file" type="file" maxlength="" />
			</div>
		</div>

		<div id="poder">
			<h3><?php echo JText::_('LBL_PODER'); ?></h3>
			<div class="form-group">
				<label for="poder-fecha-const"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('d-m-Y'),'poder-fecha-const', 'poder-fecha-const', $format = '%d-%m-%Y', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="poder-notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="poder-notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="poder-estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="poder-estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="poder-notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="poder-notario" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="poder-numero"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="poder-numero" type="text" maxlength="3" />
			</div>
			<div class="form-group">
				<label for="poder-file"><?php echo JText::_('LBL_TESTIMONIO1_FILE'); ?></label>
				<input name="poder-file" type="file" maxlength="" />
			</div>
		</div>

		<div id="registro-propiedad">
			<div class="checkbox">
        		<label><input type="checkbox"><?php echo JText::_('LBL_EN_TRAMITE'); ?></label>
			</div>

			<h3><?php echo JText::_('LBL_RPP'); ?></h3>
			<div class="form-group">
				<label for="rpp-fecha-const"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('d-m-Y'),'rpp-fecha-const', 'rpp-fecha-const', $format = '%d-%m-%Y', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="rpp-numero"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="rpp-numero" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="rpp-estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="rpp-estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="rpp-file"><?php echo JText::_('LBL_RPP_FILE'); ?></label>
				<input name="rpp-file" type="file" maxlength="" />
			</div>
		</div>


	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.endAccordion');
	?>
		<div class="form-actions">
			<button type="button" class="btn btn-primary span3" id="empresa"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
	<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'banco', JText::_('LBL_TAB_BANCO'));
	?>
	<fieldset>
        <div class="form-group">
           	<label for="banco-nombre"><?php echo JText::_('LBL_BANCOS'); ?> *:</label>
           	<select name="banco-nombre">
				<?php 
/*
				foreach ($this->catalogos->estados as $key => $value) {
					$default = ($value->nombre == 'MÃ©xico') ? 'selected' : '';
					echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
				}
*/
				?>
			</select>
        </div>
		<div class="form-group">
			<label for="banco-cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></label>
			<input name="banco-cuenta" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="banco-sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></label>
			<input name="banco-sucursal" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="banco-clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></label>
			<input name="banco-clabe" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="banco-file"><?php echo JText::_('LBL_BANCO_FILE'); ?></label>
			<input name="banco-file" type="file" maxlength="" />
		</div>

		<div class="form-actions">
			<button type="button" class="btn btn-primary span3" id="bancos"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
	</fieldset>	
	<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');
	
		echo JHtml::_('form.token');
	?>
	
</form>
<?php

// var_dump($this);
?>