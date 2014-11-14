<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document = JFactory::getDocument();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
echo '<script src="/integradora/libraries/integradora/js/sepomex.js"> </script>';
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');

?>
<script>
	jQuery(document).ready(function(){
		datosxCP("index.php?option=com_integrado&task=sepomex&format=raw");
		
		jQuery('#search').on('click', busqueda);
		jQuery('#agregarBanco').on('click', AltaBanco);
		jQuery('#altaC_P input:radio').on('click', tipoAlta);
		jQuery('button').on('click', saveCliente);
	});
	
	var integradoId	= <?php echo $this->integradoId; ?>;
	
	function ajax(parametros){
		
		var request = jQuery.ajax({
			url: parametros.link,
			data: parametros.datos,
			type: 'post'
		});
		
		return request;
	}
	
	function tipoAlta(){
		var campo 		= '';
		var display 	= '';
		var campoMonto 	= jQuery('#monto');
		
		switch( jQuery(this).prop('name') ){
			case 'typ_tipo_alta':
				if( jQuery(this).val() == 0){
					campo = '#banco';
					display = 'none';
					campoMonto.children().remove();
				}else{
					campo = '#banco';
					campoMonto.html('<label for="typ_monto"><?php echo JText::_('LBL_MONTO'); ?></label><input type="text" name="typ_monto" id="typ_monto" />');
				}
				break;
			case 'pj_pers_juridica':
				if( jQuery(this).val() == 2 ){
					campo = '#empresa';
					display = 'none';
				}else{
					campo = '#empresa';
				}
				break;
		}
		
		jQuery.each( jQuery('#altaC_P').find('a'), function(key, value){
		  if( jQuery(value).attr('href') == campo ){
		    jQuery(value).css('display',display);
		  }
		});
	}
	
	function bajaBanco(campo){
		var id		= jQuery(campo).prop('id');
		
		jQuery('#'+id).remove();
	}
	
	function busqueda(){
		var rfcBusqueda	=  jQuery('#bu_rfc').val();
			
		var envio = {
			'link'			:'?task=searchrfc&format=raw',
			'datos'			:{'rfc': rfcBusqueda, 'integradoId':integradoId}
		};
		
		var resultado = ajax(envio);
		
		resultado.done(function(response){
			if(response.success){
				mensaje = mensajes('<?php echo JText::_('MSG_FILL_FORM'); ?>', 'msg');
				llenaForm(response);
			}else{
				mensajes(response.msg, 'error')
			}
		});
	}
	
	function AltaBanco(){
		var data = jQuery('#banco').find('select, input').serialize();
			data +='&integradoId='+integradoId;
			
		var parametros = {
			'link'  : '?task=agregarBanco&format=raw',
			'datos' : data
			
		};
		
		var resultado = ajax(parametros);
		
		resultado.done(function(response){
			if(typeof(response) != 'object'){
				var obj = eval('('+response+')');
			}else{
				var obj = response;
			}
			html = '<tr id="'+obj.idCuenta+'">';
			html += '<td>'+obj.banco+'</td>';
			html += '<td>'+obj.cuenta+'</td>';
			html += '<td>'+obj.sucursal+'</td>';
			html += '<td>'+obj.clabe+'</td>';
			html += '<td><input type="button" class="btn btn-primary eliminaBanco" onClick="bajaBanco(this)" id="'+obj.idCuenta+'" value="elimina Banco" /></td>';
			html += '</tr>';
			jQuery('#banco').find('table tbody').append(html);
		});
		
	}
	
	function mensajes(msg, tipo){
		var spanError = jQuery('#errorRFC');
		
		spanError.text(msg);
		spanError.fadeIn();
		
		switch(tipo){
			case 'msg':
				spanError.delay(800).fadeOut(4000);
                jQuery('a[href="#tipo_alta"]').trigger('click');
				break;
			case 'error':
				jQuery('#bu_rfc').css('border-color', '#FF0000');
				spanError.delay(800).fadeOut(4000, function(){
					jQuery('#bu_rfc').css('border-color', '');
				});
				break;
		}
	}
	
	function llenaForm(objeto){
		if(objeto.integrado != null){
			jQuery.each(objeto.integrado, function(key,value){
				jQuery('#perFisicaMoral'+value).val(value);
				jQuery('#perFisicaMoral'+value).trigger('click');
			});
		}
		
		if(objeto.datos_personales != null){
			jQuery.each(objeto.datos_personales, function(key,value){
				jQuery('#dp_'+key).val(value);
			});
			jQuery('#dp_cod_postal').trigger('click');
		}
		
		if(objeto.datos_empresa != null){
			jQuery.each(objeto.datos_empresa, function(key,value){
				jQuery('#de_'+key).val(value);
			});
			jQuery('#de_cod_postal').trigger('click');
		}
		
		if(objeto.datos_bancarios != null){
			jQuery.each(objeto.datos_bancarios, function(key,value){
				jQuery('#db_'+key).val(value);
			});
		}
	}

    function saveCliente(){
        var tab = jQuery(this).prop('id');

        if(tab != 'agregarBanco'){
            var campos = jQuery('#altaC_P').serialize();
            campos += '&tab='+tab;
            campos += '&integradoId='+integradoId;
            campos += '&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val();

            var parametros = {
                'link'  : '?task=saveCliPro&format=raw',
                'datos' : campos
            };

            var resultado = ajax(parametros);
        }
    }
</script>
<h1><?php echo JText::_($this->titulo); ?></h1>

<form action="" class="form" id="altaC_P" name="altaC_P" method="post" enctype="multipart/form-data" >
    <input type="hidden" name="idCliPro" value="" id="idCliPro">

	<?php
		echo JHtml::_('bootstrap.startTabSet', 'tabs-clientes', array('active' => 'buscador'));
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'buscador', JText::_('COM_MANDATOS_CLIENT_ALTA_SEARCH'));
	?>
	<fieldset>
		<div class="form-group">
			<input type="text" id="bu_rfc" placeholder="Ingrese el RFC" /> <span id="errorRFC" style="display: none;"></span>
		</div>

		<div class="form-group">
			<input type="button" class="btn btn-primary" id="search" value="<?php echo JText::_("LBL_SEARCH"); ?>" />
			<input type="button" class="btn btn-danger" onclick="window.history.back()" value="<?php echo JText::_("LBL_CANCELAR"); ?>" />
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'tipo_alta', JText::_('COM_MANDATOS_CLIENT_ALTA_TYPE'));
	?>
	<fieldset>
        <input type="hidden" name="tp_status" id="tp_status" value="0">
		<div class="radio">
			<label><input type="radio" name="tp_tipo_alta" id="tipoAlta0" value="0" ><?php echo JText::_('LBL_CLIENTE'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="tp_tipo_alta" id="tipoAlta1" value="1" ><?php echo JText::_('LBL_PROVEEDOR'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="tp_tipo_alta" id="tipoAlta2" value="2" checked="checked" ><?php echo JText::_('LBL_AMBOS'); ?></label>
		</div>
		
		<div id="monto" class="form-inline">
			<label for="typ_monto"><?php echo JText::_('LBL_MONTO'); ?></label>
			<input type="text" name="tp_monto" id="tp_monto" />
		</div>
	</fieldset>
	
	<div class="form-actions">
		<button type="button" class="btn btn-primary span3" id="tipoAlta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
	</div>
	<?php
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'pers-juridica', JText::_('COM_INTEG_PERS_JURIDICA'));
	?>
	<fieldset>
		<div class="radio">
			<label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral1" value="1" checked="checked" ><?php echo JText::_('LBL_PER_MORAL'); ?></label>
		</div>
		<div class="radio">
			<label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral2" value="2" ><?php echo JText::_('LBL_PER_FISICA'); ?></label>
		</div>
	</fieldset>
	
	<div class="form-actions">
		<button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
	</div>

	<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'basic-details', JText::_('LBL_SLIDE_BASIC'));
		echo JHtml::_('bootstrap.startAccordion', 'slide-basic', array('active' => 'basic-persona'));
		echo JHtml::_('bootstrap.addSlide', 'slide-basic', JText::_('LBL_SLIDE_BASIC'), 'basic-persona');
	?>
	<fieldset>
		<div class="form-group">
			<label for="nombre"><?php echo JText::_('LBL_NOM_COMERCIAL'); ?></label>
			<input name="dp_nom_comercial" id="dp_nom_comercial" type="text" maxlength="50" value="" />
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
           			var_dump($value->id);
           			$selected = $value->id == 146?'selected':'';
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
			<label for="dp_nom_comercial"><?php echo JText::_('LBL_NOMBRE_COMPLETO'); ?></label>
			<input name="dp_nombre_representante" id="dp_nom_comercial1" type="text" maxlength="100" />
		</div>
		<div class="form-group">
			<label for="dp_curp"><?php echo JText::_('LBL_CURP'); ?></label>
			<input name="dp_curp" id="dp_curp" type="text" maxlength="18" />
		</div>
		
		<div class="form-group">
			<label for="co_tel_fijo1"><?php echo JText::_('LBL_TEL_FIJO'); ?> 1</label>
			<input name="co_tel_fijo1" id ="co_tel_fijo1" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="co_tel_fijo_extension1"><?php echo JText::_('LBL_EXT'); ?> 1</label>
			<input name="co_tel_fijo_extension1" id="co_tel_fijo_extension1" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="co_tel_movil1"><?php echo JText::_('LBL_TEL_MOVIL'); ?> 1</label>
			<input name="co_tel_movil1" id ="co_tel_movil1" type="text" maxlength="13" />
		</div>
		<div class="form-group">
			<label for="co_email1"><?php echo JText::_('LBL_CORREO'); ?> 1</label>
			<input name="co_email1" id="co_email1" type="email" maxlength="100" />
		</div>
		
		<div class="form-group">
			<label for="co_tel_fijo2"><?php echo JText::_('LBL_TEL_FIJO'); ?> 2</label>
			<input name="co_tel_fijo2" id ="co_tel_fijo2" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="co_tel_fijo_extension2"><?php echo JText::_('LBL_EXT'); ?> 2</label>
			<input name="co_tel_fijo_extension2" id="co_tel_fijo_extension2" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="co_tel_movil2"><?php echo JText::_('LBL_TEL_MOVIL'); ?> 2</label>
			<input name="co_tel_movil2" id ="co_tel_movil2" type="text" maxlength="13" />
		</div>
		<div class="form-group">
			<label for="co_email2"><?php echo JText::_('LBL_CORREO'); ?> 2</label>
			<input name="co_email2" id="co_email2" type="email1" maxlength="100" />
		</div>
		
		<div class="form-group">
			<label for="co_tel_fijo3"><?php echo JText::_('LBL_TEL_FIJO'); ?> 3</label>
			<input name="co_tel_fijo3" id ="co_tel_fijo3" type="text" maxlength="10" />
		</div>
		<div class="form-group">
			<label for="co_tel_fijo_extension3"><?php echo JText::_('LBL_EXT'); ?> 3</label>
			<input name="co_tel_fijo_extension3" id="co_tel_fijo_extension3" type="text" maxlength="5" />
		</div>
		<div class="form-group">
			<label for="co_tel_movil3"><?php echo JText::_('LBL_TEL_MOVIL'); ?> 3</label>
			<input name="co_tel_movil3" id ="co_tel_movil3" type="text" maxlength="13" />
		</div>
		<div class="form-group">
			<label for="co_email3"><?php echo JText::_('LBL_CORREO'); ?> 3</label>
			<input name="co_email3" id="co_email3" type="email" maxlength="100" />
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
		
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'empresa', JText::_('LBL_TAB_EMPRESA'));
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
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'banco', JText::_('LBL_TAB_BANCO'));
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
			<button type="button" class="btn btn-primary span3" id="agregarBanco"><?php echo JText::_('LBL_CARGAR'); ?></button>
		</div>
		
		<div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Banco</th>
						<th>Número de cuenta</th>
						<th>Número de surcusal</th>
						<th>CLABE</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>		
			</table>
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'files', JText::_('LBL_TAB_ARCHIVOS'));
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
			<button type="button" class="btn btn-primary span3" id="files"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
	</fieldset>
	<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');
	
		echo JHtml::_('form.token');
	?>
	
</form>