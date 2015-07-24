<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
jimport('integradora.notifications');

$datos = @$this->data->integrados;
$user		= JFactory::getUser();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
$optionBancos = '';

$cancelUrl = JRoute::_('index.php?option=com_integrado');
$finishUrl = $cancelUrl. '&task=finish';

$document = JFactory::getDocument();
$document->addScript('//code.jquery.com/ui/1.11.3/jquery-ui.js');
$document->addScript('libraries/integradora/js/tim-datepicker-defaults.js');
$document->addStyleSheet('//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css');

echo '<script src="libraries/integradora/js/sepomex.js"> </script>';
echo '<script src="libraries/integradora/js/tim-validation.js"> </script>';
echo '<script src="libraries/integradora/js/file_validation.js"> </script>';

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

$token = JSession::getFormToken();

?>
	<script>
		var catalogoBancos = new Array();
		<?php
	   foreach ($this->catalogos->bancos as $key => $value){
		   $optionBancos .= '<option value="'.$value->claveClabe.'">'.$value->banco.'</option>';
		   echo 'catalogoBancos["'.$value->claveClabe.'"] = "'.$value->banco.'";'." \n";
	   }
	   ?>

		function toUpper() {
			jQuery(this).val(jQuery(this).val().toUpperCase());
		}

		function deshabilitaregistroProp() {
			var checkbox = jQuery(this).prop('checked');
			var campos   = jQuery('div#registro-propiedad').find('input, select');

			jQuery.each(campos, function(key, value){
				var campo = jQuery(value);
				if( campo.prop('id') != 'tramiteRegistro' ){
					jQuery(value).prop('disabled',checkbox);
				}
			});
		}

		function activeTab(tab) {
			var tabs = jQuery('#tabs-solicitudTabs li');
			tab.removeClass('disabled');
			tab.find('a').attr("data-toggle", "tab").trigger('click');
		}

		function nextTab() {
			var tabs = jQuery('#tabs-solicitudTabs li');
			tabs.each(function (key, val) {
				var check = jQuery(val).hasClass('active');
				if( check == true) {
					nextTabObj = jQuery(tabs[key]).next();
				}

			});
			activeTab(nextTabObj);
		}

		jQuery(document).ready(function(){

			jQuery('input[type="file"]').on('change' ,{
				msg: "<?php echo JText::_('UNSUPPORTED_FILE'); ?>"
			} , file_validation );

			jQuery('#solicitud').on("keyup keypress", function(e) {
				var code = e.keyCode || e.which;
				if (code  == 13) {
					e.preventDefault();
					return false;
				}
			});

			var tabs = jQuery('#tabs-solicitudTabs li');
			var integradoIdModel = '<?php if (isset($this->data->integrados->integrado->integrado_id)) : echo $this->data->integrados->integrado->integrado_id; endif; ?>';
			if (integradoIdModel == '') {
				tabs.addClass('disabled').find('a').attr("data-toggle", "disabled");
				activeTab( tabs.first() );
			}

			jQuery('form#solicitud button.btn-primary').click(function(){
				var boton = jQuery(this).prop('id');

				if( (boton == 'juridica') || (boton == 'personales') || (boton == 'empresa') ){
					var serializado = jQuery('.tab-pane.active :input').serialize();
					datos = serializado;
					datos += '&tab='+boton;
					if( boton == 'personales' ) {
						datos += '&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val();
						datos += '&pj_personalida='+jQuery('#dp_fecha_nacimiento').val();
					}
					if (boton == 'empresa'){
						datos += '&t1_instrum_fecha='+jQuery('#t1_instrum_fecha').val();
						datos += '&t2_instrum_fecha='+jQuery('#t2_instrum_fecha').val();
						datos += '&pn_instrum_fecha='+jQuery('#pn_instrum_fecha').val();
						datos += '&rp_instrum_fecha='+jQuery('#rp_instrum_fecha').val();
					}
					datos += '&<?php echo $token; ?>=1';

					var request = jQuery.ajax({
						url: "index.php?option=com_integrado&task=saveform&format=raw",
						data: datos,
						type: 'post',
						async: false
					});

					request.done(function(result){
						if(typeof(result) != 'object'){
							var obj = eval('('+result+')');
						}else{
							var obj = result;
						}

						if(obj.safeComplete){
							messageInfo('Datos Almacenados', 'info');
							nextTab();
							if(boton == 'juridica') {
								juridica = jQuery.unserialize(serializado);

								var $filesContent = jQuery('#files');

								if(juridica.pj_pers_juridica == 1) {
									jQuery('#de_rfc').val( jQuery('#busqueda_rfc').val() ).attr('readonly', 'readonly');

									if (typeof a_empresa  !== 'undefined') {
										a_empresa.insertBefore(jQuery('a[href="#basic-details"]').parent());
									}

									jQuery('#wrap_dp_nom_comercial').hide();
									$filesContent.find('input, label').prop('disabled', false).show();
								}
								else if (juridica.pj_pers_juridica == 2) {
									nextTab();

									jQuery('#dp_rfc').val( jQuery('#busqueda_rfc').val() ).attr('readonly', 'readonly');
									a_empresa = jQuery('a[href="#empresa"]').parent().detach();

									jQuery('#wrap_dp_nom_comercial').show();
									$filesContent.find('input, label').prop('disabled', true).hide();

									$filesContent.find('input[name*="dp_"], input[name="db_banco_file"], label[for*="dp_"], label[for="db_banco_file"]').prop('disabled', false).show();
								}
								jQuery('#tabs-solicitudTabs li:first').addClass('disabled').find('a').attr('data-toggle', 'disabled');
							}
						}

						mensajesValidaciones(obj);

						jQuery('#integradoId').val(obj.integradoId);
					});

					request.fail(function (jqXHR, textStatus) {
						console.log(jqXHR, textStatus);
					});
				} else {
					if(boton == 'nextTab') {
						jQuery('#tabs-solicitudTabs').find('a[href="#files"]').trigger('click');
					}
				}
			});

			datosxCP("index.php?option=com_integrado&task=sepomex&format=raw");
			<?php
			if(!empty($datos->datos_personales)){
				if($datos->integrado->pers_juridica == 2){
				?>
			a_empresa = jQuery('a[href="#empresa"]').parent().detach();
			<?php
			}
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
				llenatablabancos(value);
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
                if(key != 'instrum_fecha') {
                    jQuery('#t2_' + key).val(value);
                }else if( (key == 'instrum_fecha') && (value != '0000-00-00') ){
                    jQuery('#t2_' + key).val(value);
                }
			});
			<?php
			}
			if(!empty($datos->poder)){
			?>
			var poder = '<?php echo json_encode($datos->poder); ?>';
			var poder = eval ("(" + poder + ")");

			jQuery.each(poder, function(key, value){
                if(key != 'instrum_fecha') {
                    jQuery('#pn_' + key).val(value);
                }else if(key == 'instrum_fecha' && value != '0000-00-00') {
                    jQuery('#pn_' + key).val(value);
                }
			});
			<?php
			}
			if(!empty($datos->reg_propiedad)){
			?>
			var reg_propiedad = '<?php echo json_encode($datos->reg_propiedad); ?>';
			var reg_propiedad = eval ("(" + reg_propiedad + ")");

			jQuery.each(reg_propiedad, function(key, value){
                if(key != 'instrum_fecha') {
                    jQuery('#rp_' + key).val(value);
                }else if(key == 'instrum_fecha' && value != '0000-00-00') {
                    jQuery('#rp_' + key).val(value);
                }
			});
			<?php
			}
			?>

			<?php
			if(!empty($datos->integrado)) {
				if (isset($datos->integrado->pers_juridica)) {
					if ($datos->integrado->pers_juridica == 1) {
						$readonlyRfc = '#de_rfc';
						$idRFC = 'de_rfc';
					} elseif ($datos->integrado->pers_juridica == 2) {
						$readonlyRfc = '#dp_rfc';
						$idRFC = 'dp_rfc';
					}
					?>
			var $rfc_not_editable = jQuery('<?php echo $readonlyRfc; ?>');
			var $value_rfc_not_editable = $rfc_not_editable.val();


			$rfc_not_editable.after('<p>' + $value_rfc_not_editable + '</p><input type="hidden" name="<?php echo $idRFC; ?>" id="<?php echo $idRFC; ?>" value="' + $value_rfc_not_editable + '" /> ').remove();

            nextTab();

            jQuery('#tabs-solicitudTabs li:first').addClass('disabled').find('a').attr('data-toggle', 'disabled');
			<?php
				}
			}
			?>

			jQuery('#agregarBanco').on('click', AltaBanco);
			jQuery('#dp_rfc, #de_rfc').on('change',toUpper);
			jQuery('#tramiteRegistro').on('change', deshabilitaregistroProp);
			jQuery('#busqueda_rfc_btn').on('click', busqueda_rfc);
			jQuery('#nextTab').on('click', nextTab);
			jQuery('#busqueda_rfc').change( function() {
				jQuery('#busqueda_rfc_btn').siblings('span.alert').remove();
				jQuery('#juridica').prop('disabled', true).addClass('disabled');
			});

			jQuery('#dp_fecha_nacimiento').datepicker({
				yearRange: "-90:-18",
				minDate: "-90y",
				maxDate: "-18y",
				changeMonth: true,
				changeYear: true
			});
			jQuery('.instrumento').datepicker({
				yearRange: "1850:<?php echo date('Y');?>",
				minDate: new Date(1850,1,1),
				maxDate: "-1d",
				changeMonth: true,
				changeYear: true
			});


		});

		function busqueda_rfc() {

			jQuery('#busqueda_rfc_btn').siblings('span.alert').remove();
			var integradoId = jQuery('#integradoId').val();
			var rfcBusqueda	=  jQuery('#busqueda_rfc').val();

			var envio = {
				'link'			:'index.php?option=com_integrado&task=search_rfc_solicitud&format=raw',
				'datos'			:{'rfc': rfcBusqueda, 'integradoId': integradoId}
			};

			var resultado = ajax(envio);

			resultado.done(function(response){
				mensajesValidaciones(response);

				if(typeof response.busqueda_rfc === 'number') {
					jQuery('#perFisicaMoral'+response.busqueda_rfc).prop('checked', true);
					jQuery('#busqueda_rfc_btn').after('<span class="alert alert-success">El RFC es correcto puede continuar hancdo click en el botón envíar.</span>');
					jQuery('#juridica').prop('disabled', false).removeClass('disabled');
				}
			});
		}

		function ajax(parametros){

			var request = jQuery.ajax({
				url: parametros.link,
				data: parametros.datos,
				type: 'post',
				async: false
			});

			return request;
		}

		function llenatablabancos(obj) {
			var fieldset = jQuery('fieldset#datosBancarios');
			fieldset.find('input').val('');
			fieldset.find('select').val(0);
			html = '<tr id="' + obj.datosBan_id + '">';
			html += '<td>' + catalogoBancos[obj.banco_codigo] + '</td>';
			html += '<td>' + obj.banco_cuenta + '</td>';
			html += '<td>' + obj.banco_sucursal + '</td>';
			html += '<td>' + obj.banco_clabe + '</td>';
			html += '<td><input type="button" class="btn btn-primary eliminaBanco" onClick="bajaBanco(this)" id="'+obj.datosBan_id+'" value="elimina Banco" /></td>';
			html += '</tr>';

			jQuery('#banco').find('table.tableBancos tbody').append(html);
		}

		function AltaBanco(){
			var idIntegradoAlta = jQuery('#idCliPro').val();
			var data = jQuery('#banco').find('select, input').serialize();
			var idIntegradoAlta = jQuery('#integradoId').val();

			data +='&integradoId='+idIntegradoAlta;

			var parametros = {
				'link'  : 'index.php?option=com_integrado&view=clientesform&task=agregarBancoSolicitud&format=raw',
				'datos' : data
			};

			var resultado = ajax(parametros);

			resultado.done(function(response){
				var obj = response;
                console.log(response);

				if(obj.success === true) {
					llenatablabancos(obj);
				}else{
//					if (obj.msg.db_banco_codigo !== true) {
//						obj.msg.db_banco_codigo.msg = 'Debe seleccionar un banco';
//					}
					mensajesValidaciones(obj.msg);
				}
			});

		}

		function bajaBanco(campo){
			var id		 = jQuery(campo).prop('id');
			var idCliPro = jQuery('#integradoId').val();
			var parametros = {
				'link'  : 'index.php?option=com_integrado&task=deleteBanco&format=raw',
				'datos' : {
					'datosBan_id' : id,
					'integradoId' : idCliPro
				}
			};
			var envioajax = ajax(parametros);

			envioajax.done(function(response){
				if(response.success) {
					jQuery('#' + id).remove();
				}else{
					alert(response.msg);
				}
			});
		}
	</script>

	<div class="msgs_plataforma" id="msgs_plataforma"></div>

    <form action="index.php?option=com_integrado&task=uploadFiles" class="form" id="solicitud" name="solicitud" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="user_id" value="<?php echo $this->data->user->id; ?>" />
        <input type="hidden" name="integradoId" id="integradoId" value="<?php echo $this->data->user->integradoId; ?>" />

		<?php
		echo JHtml::_('bootstrap.startTabSet', 'tabs-solicitud', array('active' => 'pers-juridica'));
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'pers-juridica', JText::_('LBL_INGRESA_RFC'));
		?>
		<label class="busqueda_rfc" for="busqueda_rfc">RFC:</label>
		<div class="form-group form-inline">
			<input class="busqueda_rfc form-control" id="busqueda_rfc" name="busqueda_rfc" type="text" placeholder="RFC"/>
			<a class="btn btn-primary" type="button" id="busqueda_rfc_btn"><?php echo JText::_('LBL_VALIDATE'); ?></a>
		</div>
		<fieldset>
			<div class="hidden">
				<div class="radio">
					<label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral1" value="1" <?php echo $moral; ?> /><?php echo JText::_('LBL_PER_MORAL'); ?></label>
				</div>
				<div class="radio">
					<label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral2" value="2" <?php echo $fisica; ?> /><?php echo JText::_('LBL_PER_FISICA'); ?></label>
				</div>
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-primary disabled" id="juridica" disabled><?php echo JText::_('LBL_GUARDAR'); ?></button>
			<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
		</div>

		<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'empresa', JText::_('LBL_TAB_EMPRESA'));
		?>
		<fieldset>
			<h3><?php echo JText::_('LBL_DIRECCION_FISCAL'); ?></h3>
			<div class="form-group">
				<label for="de_razon_social"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?> *</label>
				<input name="de_razon_social" id="de_razon_social" type="text" maxlength="100" />
			</div>
			<div class="form-group">
				<label for="de_rfc"><?php echo JText::_('LBL_RFC'); ?> *</label>
				<input name="de_rfc" id="de_rfc" type="text" maxlength="12" />
			</div>
		</fieldset>
		<fieldset>
			<h3><?php echo JText::_('LBL_DIRECCION_FISCAL'); ?></h3>

			<div class="form-group">
				<label for="de_calle"><?php echo JText::_('LBL_CALLE'); ?> *</label>
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
				<label for="de_cod_postal"><?php echo JText::_('LBL_CP'); ?> *</label>
				<input
					type		= "text"
					name		= "de_cod_postal"
					class		= "validate[required,custom[onlyNumberSp]] input_chica"
					id			= "de_cod_postal"
					size		= "10"
					maxlength	= "5" />
			</div>

			<div class="form-group">
				<label for="de_colonia"><?php echo JText::_('LBL_COLONIA'); ?> *</label>
				<select name="colonia" id="de_colonia" ></select>
			</div>

			<div class="form-group">
				<label for="delegacion"><?php echo JText::_('LBL_DELEGACION'); ?> *</label>
				<input
					type	= "text"
					name	= "delegacion"
					id		= "de_delegacion" />
			</div>

			<div class="form-group">
				<label for="de_estado"><?php echo JText::_('LBL_ESTADO'); ?> *</label>
				<input
					type	= "text"
					name	= "estado"
					id		= "de_estado" />
			</div>

			<div class="form-group">
				<label for="pais"><?php echo JText::_('LBL_PAIS'); ?> *</label>
				<select name="pais" id="de_pais" >
					<?php
					foreach ($this->catalogos->nacionalidades as $key => $value) {
                        $selected = $value->id == 146? ' selected="selected"' : '';
						echo '<option value="'.$value->id.'" '.$selected.'>'.$value->nombre.'</option>';
					}
					?>
				</select>
			</div>

		</fieldset>
		<fieldset>
			<div class="form-group">
				<label for="de_tel_fijo"><?php echo JText::_('LBL_TEL_FIJO'); ?> *</label>
				<input name="de_tel_fijo" id ="de_tel_fijo" type="text" maxlength="10" placeholder="Ej: 5512345678" />
			</div>
			<div class="form-group">
				<label for="de_tel_fijo_extension"><?php echo JText::_('LBL_EXT'); ?></label>
				<input name="de_tel_fijo_extension" id="de_tel_fijo_extension" type="text" maxlength="5" />
			</div>
			<div class="form-group">
				<label for="de_tel_fax"><?php echo JText::_('LBL_TEL_FAX'); ?></label>
				<input name="de_tel_fax" id ="de_tel_fax" type="text" maxlength="10" placeholder="Ej: 5512345678" />
			</div>
			<div class="form-group">
				<label for="de_sitio_web"><?php echo JText::_('LBL_SITIO_WEB'); ?></label>
				<input name="de_sitio_web" id="de_sitio_web"  maxlength="100" />
			</div>
		</fieldset>
		<fieldset>
			<div id="testimonio1">
				<h3><?php echo JText::_('LBL_TESTIMONIO1'); ?></h3>
				<div class="form-group">
					<label for="t1_instrum_fecha"><?php echo JText::_('LBL_FECHA_CONSTITUCION'); ?> *</label>
					<input type="text" name="t1_instrum_fecha" id="t1_instrum_fecha" readonly class="datepicker instrumento" />
				</div>
				<div class="form-group">
					<label for="t1_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?> *</label>
					<input name="t1_instrum_notaria" id="t1_instrum_notaria" type="text" maxlength="3" />
				</div>

				<div class="form-group">
					<label for="t1_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *</label>
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
					<label for="t1_instrum_nom_notario"><?php echo JText::_('LBL_NOTARIO'); ?> *</label>
					<input name="t1_instrum_nom_notario" id="t1_instrum_nom_notario" type="text" />
				</div>
				<div class="form-group">
					<label for="t1_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?> *</label>
					<input name="t1_instrum_num_instrumento" id="t1_instrum_num_instrumento" type="text" maxlength="10"/>
				</div>

			</div>

			<div id="testimonio2">
				<h3><?php echo JText::_('LBL_TESTIMONIO2'); ?></h3>
				<div class="form-group">
					<label for="t2_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
					<input type="text" name="t2_instrum_fecha" id="t2_instrum_fecha" readonly class="datepicker instrumento" />
				</div>
				<div class="form-group">
					<label for="t2_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
					<input name="t2_instrum_notaria" id="t2_instrum_notaria" type="text" maxlength="3" />
				</div>

				<div class="form-group">
					<label for="t2_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *</label>
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
					<input type="text" name="pn_instrum_fecha" id="pn_instrum_fecha" readonly class="datepicker instrumento" />
				</div>
				<div class="form-group">
					<label for="pn_instrum_notaria"><?php echo JText::_('LBL_NOTARIA'); ?></label>
					<input name="pn_instrum_notaria" id="pn_instrum_notaria" type="text" maxlength="3" />
				</div>

				<div class="form-group">
					<label for="pn_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *</label>
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
				<h3><?php echo JText::_('LBL_RPP'); ?></h3>
				<div class="form-group">
					<label for="rp_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
					<input type="text" name="rp_instrum_fecha" id="rp_instrum_fecha" readonly class="datepicker instrumento" />
				</div>
				<div class="form-group">
					<label for="rp_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
					<input name="rp_instrum_num_instrumento" id="rp_instrum_num_instrumento" type="text" maxlength="10" />
				</div>

				<div class="form-group">
					<label for="rp_instrum_estado"><?php echo JText::_('LBL_ESTADO'); ?> *</label>
					<select name="rp_instrum_estado" id="rp_instrum_estado">
						<?php
						foreach ($this->catalogos->estados as $key => $value) {
                            $default = ($value->nombre == 'México') ? 'selected' : '';
							echo '<option value="'.$value->id.'" '.$default.'>'.$value->nombre.'</option>';
						}
						?>
					</select>
				</div>

				<div class="checkbox">
					<label><input type="checkbox" id="tramiteRegistro"><?php echo JText::_('LBL_EN_TRAMITE'); ?></label>
				</div>
			</div>


		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="empresa"><?php echo JText::_('LBL_GUARDAR'); ?></button>
			<a class="btn btn-success" href="<?php echo $finishUrl; ?>" ><?php echo JText::_('LBL_FIN'); ?></a>
			<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
		</div>
		<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'basic-details', JText::_('LBL_SLIDE_BASIC'));
		?>
		<fieldset>
			<h3 class="accordion"></h3>
			<div class="form-group">
				<label for="dp_nombre_representante"><?php echo JText::_('LBL_NOMBRE_COMPLETO_REPRESENTANTE'); ?> *</label>
				<input name="dp_nombre_representante" id="dp_nombre_representante" type="text" maxlength="100" value="<?php echo $user->name ?>" />
			</div>
			<div class="form-group" id="wrap_dp_nom_comercial">
				<label for="dp_nom_comercial"><?php echo JText::_('LBL_NOM_COMERCIAL'); ?></label>
				<input name="dp_nom_comercial" id="dp_nom_comercial" type="text" maxlength="100" />
			</div>

			<div class="form-group">
				<label for="dp_nacionalidad"><?php echo JText::_('LBL_NACIONALIDAD'); ?> *</label>
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
				<label for="dp_sexo"><?php echo JText::_('LBL_SEXO'); ?> *</label>
				<select name="dp_sexo" id="dp_sexo">
					<option value="masculino" ><?php echo JText::_('SEXO_MASCULINO'); ?></option>
					<option value="femenino" ><?php echo JText::_('SEXO_FEMENINO'); ?></option>
				</select>
			</div>
			<div class="form-group">
				<label for="dp_fecha_nacimiento"><?php echo JText::_('LBL_FECHA_NACIMIENTO'); ?> *</label>
				<input type="text" name="dp_fecha_nacimiento" id="dp_fecha_nacimiento" class="datepicker" readonly />
			</div>
			<div class="form-group">
				<label for="dp_rfc"><?php echo JText::_('LBL_RFC').' '. JText::_('LBL_PER_FISICA').'/'. JText::_('LBL_FIRMA_3'); ?> *</label>
				<input name="dp_rfc" id="dp_rfc" type="text" maxlength="13" />
			</div>
		</fieldset>
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
				<label for="dp_num_exterior"><?php echo JText::_('NUM_EXT'); ?> *:</label>
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
				<label for="pais"><?php echo JText::_('LBL_PAIS'); ?> *</label>
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
		<fieldset>
			<div class="form-group">
				<label for="dp_tel_fijo"><?php echo JText::_('LBL_TEL_FIJO'); ?> *</label>
				<input name="dp_tel_fijo" id ="dp_tel_fijo" type="text" maxlength="10" placeholder="Ej: 5512345678" />
			</div>
			<div class="form-group">
				<label for="dp_tel_fijo_extension"><?php echo JText::_('LBL_EXT'); ?></label>
				<input name="dp_tel_fijo_extension" id="dp_tel_fijo_extension" type="text" maxlength="5" />
			</div>
			<div class="form-group">
				<label for="dp_tel_movil"><?php echo JText::_('LBL_TEL_MOVIL'); ?> *</label>
				<input name="dp_tel_movil" id ="dp_tel_movil" type="text" maxlength="13" placeholder="Ej: 0445512345678" />
			</div>
			<div class="form-group">
				<label for="email"><?php echo JText::_('LBL_CORREO'); ?> *</label>
				<input name="dp_email" id="dp_email" type="email" maxlength="100" required />
			</div>
			<div class="form-group">
				<label for="dp_curp"><?php echo JText::_('LBL_CURP'); ?> *</label>
				<input name="dp_curp" id="dp_curp" type="text" maxlength="18" />
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="personales"><?php echo JText::_('LBL_GUARDAR'); ?></button>
			<a class="btn btn-success" href="<?php echo $finishUrl; ?>" ><?php echo JText::_('LBL_FIN'); ?></a>
			<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
		</div>

		<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'banco', JText::_('LBL_TAB_BANCO'));
		?>
		<fieldset>
			<div class="form-group">
				<input type="hidden" id="datosBan_id" name="datosBan_id" value="" />
				<label for="db_banco_codigo"><?php echo JText::_('LBL_BANCOS'); ?> *</label>
				<select name="db_banco_codigo" id="db_banco_codigo">
					<option value=""><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
					<?php
					echo $optionBancos;
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="db_banco_cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?> *</label>
				<input name="db_banco_cuenta" id="db_banco_cuenta" type="text" maxlength="11" />
			</div>
			<div class="form-group">
				<label for="db_banco_sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?> *</label>
				<input name="db_banco_sucursal" id="db_banco_sucursal" type="text" maxlength="10" />
			</div>
			<div class="form-group">
				<label for="db_banco_clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?> *</label>
				<input name="db_banco_clabe" id="db_banco_clabe" type="text" maxlength="18" />
			</div>

			<div class="form-actions">
				<button type="button" class="btn btn-success" id="agregarBanco"><?php echo JText::_('LBL_CARGAR'); ?></button>
			</div>

			<div>
				<table class="table table-bordered tableBancos">
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

			<div class="form-actions">
				<button type="button" class="btn btn-primary" id="nextTab"><?php echo JText::_('LBL_GUARDAR'); ?></button>
				<a class="btn btn-success" href="<?php echo $finishUrl; ?>" ><?php echo JText::_('LBL_FIN'); ?></a>
				<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
			</div>
		</fieldset>
		<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'files', JText::_('LBL_TAB_ARCHIVOS'));
		?>
		<fieldset>
			<blockquote>
				<p><?php echo JText::sprintf('LBL_MAX_FILE_SIZE', '10MB'); ?></p>
				<p><?php echo JText::sprintf('LBL_FILE_TYPES_ALLOWED', 'JPG, PNG, GIF, y PDF'); ?></p>
			</blockquote>

			<?php
			$archivos = array(
				'dp_url_identificacion'         => array(@$datos->datos_personales->url_identificacion, 'LBL_ID_FILE'),
				'dp_url_rfc'                    => array(@$datos->datos_personales->url_rfc, 'LBL_RFC_FILE_REP_LGL'),
				'dp_url_comprobante_domicilio'  => array(@$datos->datos_personales->url_comprobante_domicilio, 'LBL_COMP_DOMICILIO_FILE'),
				'de_url_rfc'                    => array(@$datos->datos_empresa->url_rfc, 'LBL_RFC_FILE'),
				't1_url_instrumento'            => array(@$datos->testimonio1->url_instrumento, 'LBL_TESTIMONIO1_FILE'),
				't2_url_instrumento'            => array(@$datos->testimonio2->url_instrumento, 'LBL_TESTIMONIO2_FILE'),
				'pn_url_instrumento'            => array(@$datos->poder->url_instrumento, 'LBL_TESTIMONIO3_FILE'),
				'rp_url_instrumento'            => array(@$datos->reg_propiedad->url_instrumento, 'LBL_RPP_FILE'),
				'db_banco_file'                 => array(@$datos->datos_bancarios[0]->banco_file, 'LBL_BANCO_FILE')
			);

			if(isset($datos->integrado->pers_juridica)) {
				if ($datos->integrado->pers_juridica == 2) {
					unset($archivos['de_url_rfc']);
					unset($archivos['t1_url_instrumento']);
					unset($archivos['t2_url_instrumento']);
					unset($archivos['pn_url_instrumento']);
					unset($archivos['rp_url_instrumento']);
				}
			}

			foreach ( $archivos as $key => $value ) {
				?>
				<div class="form-group">
					<?php
					$noObligatorios = array('LBL_TESTIMONIO2_FILE','LBL_TESTIMONIO3_FILE','Archivo','LBL_PODER_FILE','LBL_RPP_FILE');
					$obligatorio = !in_array($value[1], $noObligatorios) ? ' *':'';

					?>
					<label for="<?php echo $key; ?>" class="head" ><?php echo JText::_($value[1]); ?><?php echo $obligatorio; ?></label>
					<input class="btn btn-default" name="<?php echo $key; ?>" type="file" maxlength="" />
					<?php
					if( isset($value[0]) ){
						?>
						<div>
							<a class="" href="<?php echo $value[0]; ?>" target="_blank"><?php echo JText::_('LBL_OPEN_FILE'); ?></a>
						</div>
					<?php
					}
					?>
				</div>
			<?php
			}
			?>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary" id="files"><?php echo JText::_('LBL_GUARDAR'); ?></button>
				<a class="btn btn-success" href="<?php echo $finishUrl; ?>" ><?php echo JText::_('LBL_FIN'); ?></a>
				<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
			</div>
		</fieldset>
		<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');

		?>

		<input type="hidden" name="<?php echo $token; ?>" value="1" />
	</form>
<?php
echo '<div class="alert alert-dismissable alert-info"><h4>'.JText::_('LBL_FORM_REQUEST_INTEGRADO_INSTRUCTIONS_TITLE').'</h4>'.JText::_('LBL_FORM_REQUEST_INTEGRADO_INSTRUCTIONS').'</div>';
