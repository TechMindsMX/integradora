<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = @$this->data->integrados[0];
//$postUrl = $datos->action->post;

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

echo '<script src="/integradora/libraries/integradora/js/sepomex.js"> </script>';

if(!empty($datos->integrado)){
	if($datos->integrado->pers_juridica == 1){
		$moral = 'checked="checked"';
		$fisica = '';
	}elseif($datos->integrado->pers_juridica == 2){
		$fisica = 'checked="checked"';
		$moral = '';
	}elseif($datos->integrado->pers_juridica == 0){
		$fisica = '';
		$moral = '';
	}
}else{
	$fisica = '';
	$moral = '';
}
?>
<script>
	jQuery(document).ready(function(){
		datosxCP("index.php?option=com_integrado&task=sepomex&format=raw");
		<?php
		if(!empty($datos->datos_personales)){
		?>
			var datos_personales = '<?php echo json_encode($datos->datos_personales); ?>';
			var datos_personales = eval ("(" + datos_personales + ")");

			jQuery.each(datos_personales, function(key, value){
				jQuery('#dp_'+key).val(value);
			});
			
			jQuery('#dp_cod_postal').trigger('click');
		<?php
		}
		if(!empty($datos->datos_empresa)){
		?>
			var datos_empresa = '<?php echo json_encode($datos->datos_empresa); ?>';
			var datos_empresa = eval ("(" + datos_empresa + ")");

				
			jQuery.each(datos_empresa, function(key, value){
				jQuery('#de_'+key).val(value);
			});
			
			jQuery('#de_cod_postal').trigger('click');
		<?php
		}
		if(!empty($datos->datos_bancarios)){
		?>
			var datos_bancarios = '<?php echo json_encode($datos->datos_bancarios); ?>';
			var datos_bancarios = eval ("(" + datos_bancarios + ")");

			jQuery.each(datos_bancarios, function(key, value){
				jQuery('#db_'+key).val(value);
			});
		<?php
		}
		if(!empty($datos->testimonio1)){
		?>
			var testimonio_1 = '<?php echo json_encode($datos->testimonio1); ?>';
			var testimonio_1 = eval ("(" + testimonio_1 + ")");

			jQuery.each(testimonio_1, function(key, value){
				jQuery('#t1_'+key).val(value);
			});
		<?php
		}
		if(!empty($datos->testimonio2)){
		?>
			var testimonio_2 = '<?php echo json_encode($datos->testimonio2); ?>';
			var testimonio_2 = eval ("(" + testimonio_2 + ")");

			jQuery.each(testimonio_2, function(key, value){
				jQuery('#t2_'+key).val(value);
			});
		<?php
		}
		if(!empty($datos->poder)){
		?>
			var poder = '<?php echo json_encode($datos->poder); ?>';
			var poder = eval ("(" + poder + ")");

			jQuery.each(poder, function(key, value){
				jQuery('#pn_'+key).val(value);
			});
		<?php
		}
		if(!empty($datos->reg_propiedad)){
		?>
			var reg_propiedad = '<?php echo json_encode($datos->reg_propiedad); ?>';
			var reg_propiedad = eval ("(" + reg_propiedad + ")");

			jQuery.each(reg_propiedad, function(key, value){
				jQuery('#rp_'+key).val(value);
			});
		<?php
		}
		?>
	});
</script>

<form action="index.php?option=com_integrado&task=uploadFiles" class="form" id="solicitud" name="solicitud" method="post" enctype="multipart/form-data" >
	<input type="hidden" name="user_id" value="<?php echo $this->data->user->id; ?>" />
	<?php
		echo JHtml::_('bootstrap.startTabSet', 'tabs-solicitud', array('active' => 'pers-juridica'));
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'pers-juridica', JText::_('COM_INTEG_PERS_JURIDICA'));
	?>
	<fieldset>
		<div class="radio">
			<label><input type="radio" name="pers_juridica" id="perFisicaMoral1" value="1" <?php echo $moral; ?> /><?php echo JText::_('LBL_PER_MORAL'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="pers_juridica" id="perFisicaMoral2" value="2" <?php echo $fisica; ?> /><?php echo JText::_('LBL_PER_FISICA'); ?></label>
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
			<input name="apePat" type="text" maxlength="50" value="" />
		</div>
		<div class="form-group">
			<label for="apeMat"><?php echo JText::_('APE_MAT'); ?></label>
			<input name="apeMat" type="text" maxlength="50" />
		</div>
		<div class="form-group">
			<label for="nombre"><?php echo JText::_('LBL_NOMBRE'); ?></label>
			<input name="nombre" type="text" maxlength="50" value="<?php echo isset($datos->user)?$datos->user->name:''; ?>" />
		</div>
		<div class="form-group">
			<label for="dp_nacionalidad"><?php echo JText::_('LBL_NACIONALIDAD'); ?></label>
			<select name="dp_nacionalidad" id="dp_nacionalidad">
				<?php 
				foreach ($this->catalogos->nacionalidades as $key => $value) {
					$default = ($value->nombre == 'México') ? 'selected' : '';
					echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="dp_sexo"><?php echo JText::_('LBL_SEXO'); ?></label>
			<select name="dp_sexo" id="dp_sexo">
				<option value="masculino" ><?php echo JText::_('SEXO_MASCULINO'); ?></option>
				<option value="femenino" ><?php echo JText::_('SEXO_FEMENINO'); ?></option>
			</select>
		</div>
		<div class="form-group">
			<label for="dp_fecha_nacimiento"><?php echo JText::_('LBL_FECHA_NACIMIENTO'); ?></label>
			<?php 
			$default = date('Y-m-d');
			echo JHTML::_('calendar',$default,'dp_fecha_nacimiento', 'dp_fecha_nacimiento', $format = '%Y-%m-%d', $attsCal);
			?>
		</div>
		<div class="form-group">
			<label for="dp_rfc"><?php echo JText::_('LBL_RFC'); ?></label>
			<input name="dp_rfc" id="dp_rfc" type="text" maxlength="18" />
		</div>
	</fieldset>	
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_DIRECCION'), 'basic-direccion');
	?>
	<fieldset>
		<div class="form-group">
           	<label for="dp_calle"><?php echo JText::_('LBL_CALLE'); ?> *:</label>
            <input 
            	name		="dp_calle" 
            	class		="validate[required,custom[onlyLetterNumber]]" 
            	type		="text" 
            	id			="dp_calle" 
            	maxlength	="70" />
        </div>
        
        <div class="form-group">
           	<label for="dp_num_exterior"><?php echo JText::_('NUM_EXT'); ?>*:</label>
           	<input 
           		name		="dp_num_exterior" 
           		class		="validate[required,custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="dp_num_exterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="dp_num_interior"><?php echo JText::_('NUM_INT'); ?>:</label>
           	<input 
           		name		="dp_num_interior" 
           		class		="validate[custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="dp_num_interior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
        <label for="dp_cod_postal"><?php echo JText::_('LBL_CP'); ?> *:</label>
       	<input 
       		type		= "text"
       		name		= "dp_cod_postal"
       		class		= "validate[required,custom[onlyNumberSp]] input_chica"
       		id			= "dp_cod_postal"
       		size		= "10"
       		maxlength	= "5" />
    	</div>
    	<div class="form-group">
	       	<label for="dp_colonia"><?php echo JText::_('LBL_COLONIA'); ?> *:</label>
    	   	<select name="colonia" id="dp_colonia" ></select>
    	</div>
    	
    	<div class="form-group">
	    	<label for="delegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *:</label>
	    	<input 
	    		type	= "text" 
	    		name	= "delegacion"
	    		id		= "dp_delegacion" />
   		</div>
   		
   		<div class="form-group">
       		<label for="dp_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
       		<input 
       			type	= "text"
       			name	= "estado"
       			id		= "dp_estado" />
    	</div>

        <div class="form-group">
           	<label for="pais"><?php echo JText::_('LBL_PAIS'); ?> *:</label>
           	<select name="pais" id="pais" >
           		<?php
           		foreach ($this->catalogos->nacionalidades as $key => $value) {
           			$selected = $value->id == 146?'selected="selected"':'';
           			echo '<option value="'.$value->id.'" '.$selected.'>'.$value->nombre.'</option>';
				}
           		?>
			</select>
		</div>
		
	</fieldset>

	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_EXTRAS'), 'ext-details');
	?>
	<fieldset>
		<div class="form-group">
			<label for="dp_tel_fijo"><?php echo JText::_('LBL_TEL_FIJO'); ?></label>
			<input name="dp_tel_fijo" id ="dp_tel_fijo" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="dp_tel_fijo_extension"><?php echo JText::_('LBL_EXT'); ?></label>
			<input name="dp_tel_fijo_extension" id="dp_tel_fijo_extension" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="dp_tel_movil"><?php echo JText::_('LBL_TEL_MOVIL'); ?></label>
			<input name="dp_tel_movil" id ="dp_tel_movil" type="text" maxlength="13" />
		</div>
		<div class="form-group">
			<label for="email"><?php echo JText::_('LBL_CORREO'); ?></label>
			<input name="dp_email" id="dp_email" type="email" maxlength="100" />
		</div>
		<div class="form-group">
			<label for="dp_nom_comercial"><?php echo JText::_('LBL_NOM_COMERCIAL'); ?></label>
			<input name="dp_nom_comercial" id="dp_nom_comercial" type="text" maxlength="100" />
		</div>
		<div class="form-group">
			<label for="dp_curp"><?php echo JText::_('LBL_CURP'); ?></label>
			<input name="dp_curp" id="dp_curp" type="text" maxlength="18" />
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
			<label for="de_razon_social"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?></label>
			<input name="de_razon_social" id="de_razon_social" type="text" maxlength="100" />
		</div>
		<div class="form-group">
			<label for="de_rfc"><?php echo JText::_('LBL_RFC'); ?></label>
			<input name="de_rfc" id="de_rfc" type="text" maxlength="18" />
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.addSlide', 'empresa', JText::_('LBL_SLIDE_DIRECCION'), 'empresa-direccion');
	?>
	<fieldset>
		<div class="form-group">
           	<label for="de_calle"><?php echo JText::_('LBL_CALLE'); ?> *:</label>
            <input 
            	name		="de_calle" 
            	class		="validate[required,custom[onlyLetterNumber]]" 
            	type		="text" 
            	id			="de_calle" 
            	maxlength	="70" />
        </div>
        
        <div class="form-group">
           	<label for="num_exterior"><?php echo JText::_('NUM_EXT'); ?>*:</label>
           	<input 
           		name		="de_num_exterior" 
           		class		="validate[required,custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="de_num_exterior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
           	<label for="de_num_interior"><?php echo JText::_('NUM_INT'); ?>:</label>
           	<input 
           		name		="de_num_interior" 
           		class		="validate[custom[onlyLetterNumber]]" 
           		type		="text" 
           		id			="de_num_interior" 
           		size		="10" 
           		maxlength	="5" />
        </div>
        
        <div class="form-group">
        <label for="de_cod_postal"><?php echo JText::_('LBL_CP'); ?> *:</label>
       	<input 
       		type		= "text"
       		name		= "de_cod_postal"
       		class		= "validate[required,custom[onlyNumberSp]] input_chica"
       		id			= "de_cod_postal"
       		size		= "10"
       		maxlength	= "5" />
    	</div>
    	
    	<div class="form-group">
	       	<label for="de_colonia"><?php echo JText::_('LBL_COLONIA'); ?> *:</label>
    	   	<select name="colonia" id="de_colonia" ></select>
    	</div>
    	
    	<div class="form-group">
	    	<label for="delegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *:</label>
	    	<input 
	    		type	= "text" 
	    		name	= "delegacion"
	    		id		= "de_delegacion" />
   		</div>
   		
   		<div class="form-group">
       		<label for="de_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
       		<input 
       			type	= "text"
       			name	= "estado"
       			id		= "de_estado" />
    	</div>

        <div class="form-group">
           	<label for="pais"><?php echo JText::_('LBL_PAIS'); ?> *:</label>
           	<select name="pais" id="de_pais" >
           		<?php
           		foreach ($this->catalogos->nacionalidades as $key => $value) {
           			$selected = $value->id == 146?'selected="selected"':'';
           			echo '<option value="'.$value->id.'" '.$selected.'>'.$value->nombre.'</option>';
				}
           		?>
			</select>
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
				<label for="t1_instrum_fecha"><?php echo JText::_('LBL_FECHA_CONSTITUCION'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('Y-m-d'),'t1_instrum_fecha', 't1_instrum_fecha', $format = '%Y-%m-%d', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="t1_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="t1_instrum_notaria" id="t1_instrum_notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="t1_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="t1_instrum_estado" id="t1_instrum_estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="t1_instrum_nom_notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="t1_instrum_nom_notario" id="t1_instrum_nom_notario" type="text" />
			</div>
			<div class="form-group">
				<label for="t1_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="t1_instrum_num_instrumento" id="t1_instrum_num_instrumento" type="text" maxlength="10"/>
			</div>
			
		</div>

		<div id="testimonio2">
			<h3><?php echo JText::_('LBL_TESTIMONIO2'); ?></h3>
			<div class="form-group">
				<label for="t2_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('Y-m-d'),'t2_instrum_fecha', 't2_instrum_fecha', $format = '%Y-%m-%d', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="t2_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="t2_instrum_notaria" id="t2_instrum_notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="t2_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="t2_instrum_estado" id="t2_instrum_estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="t2_instrum_nom_notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="t2_instrum_nom_notario" id="t2_instrum_nom_notario" type="text" maxlength="" />
			</div>
			<div class="form-group">
				<label for="t2_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="t2_instrum_num_instrumento" id="t2_instrum_num_instrumento" type="text" maxlength="10" />
			</div>
		</div>

		<div id="poder">
			<h3><?php echo JText::_('LBL_PODER'); ?></h3>
			<div class="form-group">
				<label for="pn_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('Y-m-d'),'pn_instrum_fecha', 'pn_instrum_fecha', $format = '%Y-%m-%d', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="pn_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
				<input name="pn_instrum_notaria" id="pn_instrum_notaria" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="pn_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="pn_instrum_estado" id="pn_instrum_estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
	        </div>
	
			<div class="form-group">
				<label for="pn_instrum_nom_notario"><?php echo JText::_('LBL_NOTARIO'); ?></label>
				<input name="pn_instrum_nom_notario" id="pn_instrum_nom_notario" type="text" maxlength="" />
			</div>
			<div class="form-group">
				<label for="pn_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="pn_instrum_num_instrumento" id="pn_instrum_num_instrumento" type="text" maxlength="10" />
			</div>
		</div>

		<div id="registro-propiedad">
			<div class="checkbox">
        		<label><input type="checkbox"><?php echo JText::_('LBL_EN_TRAMITE'); ?></label>
			</div>

			<h3><?php echo JText::_('LBL_RPP'); ?></h3>
			<div class="form-group">
				<label for="rp_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
				<?php 
				echo JHTML::_('calendar',date('Y-m-d'),'rp_instrum_fecha', 'rp_instrum_fecha', $format = '%Y-%m-%d', $attsCal);
				?>
			</div>
			<div class="form-group">
				<label for="rp_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
				<input name="rp_instrum_num_instrumento" id="rp_instrum_num_instrumento" type="text" maxlength="3" />
			</div>
	 
	        <div class="form-group">
	           	<label for="rp_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *:</label>
	           	<select name="rp_instrum_estado" id="rp_instrum_estado">
					<?php 
					foreach ($this->catalogos->estados as $key => $value) {
						$default = ($value->nombre == 'México') ? 'selected' : '';
						echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
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
           	<label for="db_banco_nombre"><?php echo JText::_('LBL_BANCOS'); ?> *:</label>
           	<select name="db_banco_nombre" id="db_banco_nombre">
           		<option><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
				<?php 
				foreach ($this->catalogos->bancos as $key => $value) {
					echo '<option value="'.$value->claveClabe.'">'.$value->banco.'</option>';
				}
				?>
			</select>
        </div>
		<div class="form-group">
			<label for="db_banco_cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></label>
			<input name="db_banco_cuenta" id="db_banco_cuenta" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="db_banco_sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></label>
			<input name="db_banco_sucursal" id="db_banco_sucursal" type="text" maxlength="3" />
		</div>
		<div class="form-group">
			<label for="db_banco_clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></label>
			<input name="db_banco_clabe" id="db_banco_clabe" type="text" maxlength="18" />
		</div>
		

		<div class="form-actions">
			<button type="button" class="btn btn-primary span3" id="bancos"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'files', JText::_('LBL_TAB_ARCHIVOS'));
	?>
	<fieldset>
		
		<div class="form-group">
			<label for="dp_url_identificacion"><?php echo JText::_('LBL_ID_FILE'); ?></label>
			<input name="dp_url_identificacion" type="file" maxlength="" />
		</div>
		<div class="form-group">
			<label for="dp_url_rfc"><?php echo JText::_('LBL_RFC_FILE'); ?></label>
			<input name="dp_url_rfc" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
			<label for="dp_url_comprobante_domicilio"><?php echo JText::_('LBL_COMP_DOMICILIO_FILE'); ?></label>
			<input name="dp_url_comprobante_domicilio" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
			<label for="de_url_rfc"><?php echo JText::_('LBL_RFC_FILE'); ?></label>
			<input name="de_url_rfc" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
			<label for="t1_url_instrumento"><?php echo JText::_('LBL_TESTIMONIO1_FILE'); ?></label>
			<input name="t1_url_instrumento" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
			<label for="t2_url_instrumento"><?php echo JText::_('LBL_TESTIMONIO2_FILE'); ?></label>
			<input name="t2_url_instrumento" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
			<label for="pn_url_instrumento"><?php echo JText::_('LBL_TESTIMONIO1_FILE'); ?></label>
			<input name="pn_url_instrumento" type="file" maxlength="" />
		</div>
		
		<div class="form-group">
				<label for="rp_url_instrumento"><?php echo JText::_('LBL_RPP_FILE'); ?></label>
				<input name="rp_url_instrumento" type="file" maxlength="" />
			</div>

        <div class="form-group">
			<label for="db_banco_file"><?php echo JText::_('LBL_BANCO_FILE'); ?></label>
			<input name="db_banco_file" type="file" maxlength="" />
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary span3" id="files"><?php echo JText::_('LBL_ENVIAR'); ?></button>
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