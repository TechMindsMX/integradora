<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$datos = $this->datos;
$document = JFactory::getDocument();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');

$document->addScript('//code.jquery.com/ui/1.11.3/jquery-ui.js');
$document->addScript('libraries/integradora/js/tim-datepicker-defaults.js');
$document->addStyleSheet('//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css');

$optionBancos = '';
$token = JSession::getFormToken();

if (isset($datos->rfc)) {
	$rfcSearch = $datos->rfc;
} elseif (isset($datos->pRFC)) {
	$rfcSearch = $datos->pRFC;
}

echo '<script src="libraries/integradora/js/sepomex.js"> </script>';
echo '<script src="libraries/integradora/js/tim-validation.js"> </script>';
echo '<script src="libraries/integradora/js/file_validation.js"> </script>';

?>

<script xmlns="http://www.w3.org/1999/html">
    var catalogoBancos = [];
    var integradoId	= <?php echo $this->integradoId; ?>;
    var formulario = '';

    <?php
    foreach ($this->catalogos->bancos as $key => $value){
        $optionBancos .= '<option value="'.$value->claveClabe.'">'.$value->banco.'</option>';
        echo 'catalogoBancos["'.$value->claveClabe.'"] = "'.$value->banco.'";'." \n";
	}
    ?>

	jQuery(document).ready(function(){

		jQuery('#search').on('click', busqueda_rfc);

		tabs = jQuery('#tabs-clientesTabs li');
		detached = [];

		formulario = jQuery('#container-form').clone().html();

		<?php
		if(isset($rfcSearch) ){
			echo 'jQuery("#bu_rfc").val("'.$rfcSearch.'");'."\n";
			echo 'jQuery("#search").trigger("click");'."\n";

		} else {
		?>
			tabs.addClass('disabled').find('a').attr("data-toggle", "disabled");
			activeTab( tabs.first() );

		<?php
		}
		if(!empty($datos->bancos)){
			echo "var objeto = ".json_encode($datos->bancos).';';
			echo "jQuery.each(objeto, function(key, value){
				llenatablabancos(value);
			})";
		}
		?>
    });

    function makeBinds() {
	    jQuery('input[type="file"]').on('change' ,{
		    msg: "<?php echo JText::_('UNSUPPORTED_FILE'); ?>"
	    } , file_validation );
	    jQuery('#nextTab').click('click', nextTab);
	    jQuery('#agregarBanco').on('click', AltaBanco);
	    jQuery('#tipo_alta_cp input:radio').on('click', tipoAlta);
	    jQuery('button.envio').on('click', saveCliente);
	    jQuery('#tramiteRegistro').on('change', deshabilitaregistroProp);
	    datosxCP("index.php?option=com_integrado&task=sepomex&format=raw");
	    jQuery('#dp_fecha_nacimiento').datepicker({
		    yearRange: "-90:-18",
		    minDate: "-90y",
		    maxDate: "-18y",
		    changeMonth: true,
		    changeYear: true
	    });
	    jQuery('.instrumento').datepicker({
		    yearRange: "1850:0",
		    minDate: new Date(1850,1,1),
		    maxDate: "-1d",
		    changeMonth: true,
		    changeYear: true
	    });
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
		var campoMonto 	= jQuery('#monto');
		var nombreCampo = jQuery(this).prop('name');

		switch( nombreCampo ){
			case 'tp_tipo_alta':
				if( jQuery(this).val() == 0){
					campo = '#banco';
					extractTab(campo);
					campoMonto.children().remove();
				}else{
					campo = '#banco';
					extractTab(campo);
					attachTab(campo);
					campoMonto.html('<label for="tp_monto"><?php echo JText::_('LBL_MONTO'); ?></label><input type="text" name="tp_monto" id="tp_monto" />');
				}
				break;
			case 'pj_pers_juridica':
				if( jQuery(this).val() == 2 ){
					campo = '#empresa';
					extractTab(campo);
				}else{
					campo = '#empresa';
					attachTab(campo);
				}
				break;
            default :
                break;
		}
    }

    function busqueda_rfc(){
		var rfcBusqueda	=  jQuery('#bu_rfc').val();

		var envio = {
			'link'			:'index.php?option=com_integrado&task=search_rfc_cliente&format=raw',
			'datos'			:{'rfc': rfcBusqueda, 'integradoId':integradoId}
		};

		var resultado = ajax(envio);

		resultado.done(function(response){

			jQuery('#container-form').empty().append(formulario);
			makeBinds();

			if(response.success == true){
                <?php //Existe el rfc y se llena el form ?>
				llenaForm(response);
				var campo = ['#pers-juridica', '#basic-details', '#empresa', '#files'];
				jQuery.each(campo, function(k,v) {
					extractTab(v);
				});
				activeTab( '#tipo_alta' );

			} else if (response.bu_rfc.success == 'invalid') {
                <?php //RFC MAL ?>
				jQuery('input, select, textarea').prop("readonly", false);

				<?php /* override para la fincion de mensajes de validacion */ ?>
				response.bu_rfc.success = false;
				mensajesValidaciones(response);
				jQuery('#container-form').empty();

			} else {
                <?php //No existe el RFC DESEA DARLO DE ALTA? ?>
				var radio = response.bu_rfc;
				jQuery('#perFisicaMoral' + radio).trigger('click');
				if (response.bu_rfc == 1) {
					jQuery('#tipo_pers_juridica').html('Personalidad juridica: Moral');
					jQuery('#de_rfc').val(jQuery('#bu_rfc').val()).attr('readonly', 'readonly');
				}
				else {
					jQuery('#tipo_pers_juridica').html('Personalidad juridica: Física');
					jQuery('#dp_rfc').val(jQuery('#bu_rfc').val()).attr('readonly', 'readonly');
					extractTab('#empresa');
				}
				var msg = '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo JText::_('LBL_NUEVO_CLIENTE'); ?></div>';
				jQuery('#tipo_alta').prepend(msg);

				var the_tabs = jQuery('#tabs-clientesTabs');
				the_tabs.find('li').addClass('disabled');
				the_tabs.find('a').attr("data-toggle", "");
				activeTab( '#tipo_alta' );
			}
		});
	}
    function llenatablabancos(obj) {
        var fieldset = jQuery('fieldset#datosBancarios');
        fieldset.find('input:not(.eliminaBanco)').val('');
        fieldset.find('select').val(0);
        var $html = '<tr id="' + obj.datosBan_id + '">';
        $html += '<td>' + catalogoBancos[obj.banco_codigo] + '</td>';
        $html += '<td>' + obj.banco_cuenta + '</td>';
        $html += '<td>' + obj.banco_sucursal + '</td>';
        $html += '<td>' + obj.banco_clabe + '</td>';
        $html += '<td><input type="button" class="btn btn-primary eliminaBanco" onClick="bajaBanco(this)" id="'+obj.datosBan_id+'" value="elimina Banco" /></td>';
        $html += '</tr>';

        jQuery('#banco').find('table.tableBancos tbody').append($html);
    }

    function AltaBanco(){
        var idIntegradoAlta = jQuery('#idCliPro').val();
		var data = jQuery('#banco').find('select, input').serialize();
			data +='&integradoId='+idIntegradoAlta;

		var parametros = {
			'link'  : 'index.php?option=com_mandatos&view=clientesform&task=agregarBancoCliente&format=raw',
			'datos' : data

		};

		var resultado = ajax(parametros);

		resultado.done(function(response){
			var obj = response;

            if(obj.success === true) {
                llenatablabancos(obj);
	            habilita_fin();
            }else{
	            if (typeof obj.msg.db_banco_codigo !== 'undefined'){
		            if (obj.msg.db_banco_codigo !== true) {
			            obj.msg.db_banco_codigo.msg = 'Debe seleccionar un banco';
		            }
	            }
	            mensajesValidaciones(obj.msg);
            }
		});

	}

    function bajaBanco(campo){
        var id		 = jQuery(campo).prop('id');
        var idCliPro = jQuery('#idCliPro').val();
        var parametros = {
            'link'  : 'index.php?option=com_mandatos&view=clientesform&task=deleteBanco&format=raw',
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
            jQuery('#idCliPro').val(objeto.integrado.integrado_id);

			jQuery.each(objeto.integrado, function(key,value){
                if(key == 'pers_juridica') {
                    jQuery('#perFisicaMoral' + value).prop('checked', true);
                    jQuery('#perFisicaMoral' + value).trigger('click');
                }

                jQuery('input[id*="perFisicaMoral"]').prop('disabled', true);
			});
		}

		if(objeto.datos_personales != null){
			jQuery.each(objeto.datos_personales, function(key,value){
				jQuery('#dp_'+key).val(value);
                jQuery('#dp_'+key).prop('readOnly',true);
			});
			jQuery('#dp_cod_postal').trigger('click');
		}

		if(objeto.datos_empresa != null){
			jQuery.each(objeto.datos_empresa, function(key,value){
				jQuery('#de_'+key).val(value);
                jQuery('#de_'+key).prop('readOnly', true);
			});
			jQuery('#de_cod_postal').trigger('click');
		}

		if(objeto.tipo_alta != null) {
			jQuery("#tipoAlta"+objeto.tipo_alta).trigger("click");
		}

		if(objeto.monto != null) {
			jQuery("#tp_monto").val(objeto.monto);
		}
	}
    function saveCliente(){
        var tab = jQuery(this).prop('id');

        if(tab != 'agregarBanco'){
            var campos = jQuery('#altaC_P').serialize();
            campos += '&tab='+tab;
	        campos += '&integradoId='+integradoId;
	        campos += '&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val();
	        campos += '&t1_instrum_fecha='+jQuery('#t1_instrum_fecha').val();
	        campos += '&t2_instrum_fecha='+jQuery('#t2_instrum_fecha').val();
	        campos += '&pn_instrum_fecha='+jQuery('#pn_instrum_fecha').val();
	        campos += '&rp_instrum_fecha='+jQuery('#rp_instrum_fecha').val();
	        campos += '&'+jQuery('input[name="pj_pers_juridica"]').serialize();
	        campos += '&<?php echo $token; ?>=1';

            var parametros = {
                'link'  : 'index.php?option=com_mandatos&view=clientesform&task=saveCliPro&format=raw',
                'datos' : campos
            };

            var resultado = ajax(parametros);

            resultado.done(function(response){
               if(response.success === true){
                   var spanMsg = jQuery('#msg');
                   jQuery('#idCliPro').val(response.idCliPro);
                   nextTab();
                   spanMsg.text('Datos Almacenados');
                   spanMsg.fadeIn();
                   spanMsg.fadeOut(8000);
               } else {
	               mensajesValidaciones(response);
               }
            });
        }
    }

    function attachTab(campo) {
	    var tabs = jQuery(formulario).find('#tabs-clientesTabs li');
	    console.log(tabs.length);
	    var lastTab = jQuery('#tabs-clientesTabs li:last-of-type');

	    jQuery.each(tabs, function (key, value) {
		    li_href = jQuery(value).find('a').attr('href');
		    if ((li_href == campo)) {
			    if (campo == '#banco') {
				    if (jQuery('#tabs-clientesTabs').find('li').length > 1) {
					    jQuery(lastTab).before(value);
				    } else {
					    jQuery(lastTab).after(value);
				    }
			    }
			    if (campo == '#empresa') {
				    jQuery(tabs[key - 1]).after(detached[key]);
			    }
		    }
	    });
    }

    function extractTab(campo) {
	    li_href = jQuery('a[href="'+campo+'"]').parent();
	    detached[campo] = jQuery(li_href).detach();
    }

    function activeTab(campo) {
	    var tab_li = jQuery('a[href="'+campo+'"]').parent();
	    tab_li.removeClass('disabled');
	    tab_li.find('a').attr("data-toggle", "tab").trigger('click');
    }

    function nextTab() {
	    var tabs = jQuery('#tabs-clientesTabs li');

	    tabs.each(function (key, val) {
		    var check = jQuery(val).hasClass('active');
		    if( check == true) {
			    nextTabObj = jQuery(tabs[key]).next().find('a').attr('href');
		    }
	    });
	    activeTab(nextTabObj);
    }

	function habilita_fin() {
		jQuery('#btn_fin').prop('href', 'index.php?option=com_mandatos&view=clienteslist').removeClass('disabled');
	}

</script>

<span id="msg" style="display: none;"></span>
<h1><?php echo JText::_($this->titulo); ?></h1>

<fieldset>
	<div class="form-group">
		<input type="text" id="bu_rfc" class="form-control" placeholder="Ingrese el RFC" maxlength="13" /> <span id="errorRFC" style="display: none;"></span>
		<input type="button" class="btn btn-primary form-control" id="search" value="<?php echo JText::_("LBL_SEARCH"); ?>" />
	</div>
</fieldset>

<div id="container-form" class="form-actions">
	<form action="index.php?option=com_mandatos&task=uploadFiles" class="form" id="altaC_P" name="altaC_P" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="idCliPro" value="<?php echo $datos->id; ?>" id="idCliPro">

		<?php
		echo JHtml::_('bootstrap.startTabSet', 'tabs-clientes', array('active' => JText::_('COM_MANDATOS_CLIENT_ALTA_TYPE') ));
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'tipo_alta', JText::_('COM_MANDATOS_CLIENT_ALTA_TYPE') );
		?>
		<fieldset id="tipo_alta_cp">
			<input type="hidden" name="tp_status" id="tp_status" value="<?php echo $datos->status ?>">
			<div class="radio">
				<label><input type="radio" name="tp_tipo_alta" id="tipoAlta0" value="0" ><?php echo JText::_('LBL_CLIENTE'); ?></label>
			</div>
			<div class="radio">
				<label><input type="radio" name="tp_tipo_alta" id="tipoAlta1" value="1" ><?php echo JText::_('LBL_PROVEEDOR'); ?></label>
			</div>
			<div class="radio">
				<label><input type="radio" name="tp_tipo_alta" id="tipoAlta2" value="2" ><?php echo JText::_('LBL_AMBOS'); ?></label>
			</div>

			<div id="monto" class="form-inline" >
				<label for="tp_monto"><?php echo JText::_('LBL_MONTO'); ?> *</label>
				<input type="text" name="tp_monto" id="tp_monto" />
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-primary envio" id="tipoAlta_btn"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
		<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'pers-juridica', JText::_('COM_INTEG_PERS_JURIDICA'));
		?>
		<fieldset>
			<div>
				<p id="tipo_pers_juridica"></p>
			</div>
			<div class="hidden">
				<div class="radio">
					<input type="radio" name="pj_pers_juridica" id="perFisicaMoral1" value="1" ><label><?php echo JText::_('LBL_PER_MORAL'); ?></label>
				</div>
				<div class="radio">
					<input type="radio" name="pj_pers_juridica" id="perFisicaMoral2" value="2" ><label><?php echo JText::_('LBL_PER_FISICA'); ?></label>
				</div>
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-primary envio" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
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
				<label for="dp_rfc"><?php echo JText::_('LBL_RFC'); ?> *</label>
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
				<label for="dp_nom_comercial"><?php echo JText::_('LBL_NOMBRE_COMPLETO'); ?> *</label>
				<input name="dp_nombre_representante" id="dp_nombre_representante" type="text" maxlength="100" />
			</div>
			<div class="form-group">
				<label for="dp_curp"><?php echo JText::_('LBL_CURP'); ?> *</label>
				<input name="dp_curp" id="dp_curp" type="text" maxlength="18" />
			</div>

			<div class="form-group">
				<label for="co_tel_fijo1"><?php echo JText::_('LBL_TEL_FIJO'); ?> 1 *</label>
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
				<label for="co_email1"><?php echo JText::_('LBL_CORREO'); ?> 1 *</label>
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
			<button type="button" class="btn btn-primary envio" id="personales"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>

		<?php
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'empresa', JText::_('LBL_TAB_EMPRESA'));
		echo JHtml::_('bootstrap.startAccordion', 'empresa', array('active' => 'empresa-nombre'));
		echo JHtml::_('bootstrap.addSlide', 'empresa', JText::_('LBL_SLIDE_GENERALES'), 'empresa-nombre');
		?>
		<fieldset>
			<div class="form-group">
				<label for="de_razon_social"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?> *</label>
				<input name="de_razon_social" id="de_razon_social" type="text" maxlength="100" />
			</div>
			<div class="form-group">
				<label for="de_rfc"><?php echo JText::_('LBL_RFC'); ?> *</label>
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
					<input type="text" name="t2_instrum_fecha" id="t2_instrum_fecha" class="datepicker instrumento" readonly />
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
					<input type="text" name="pn_instrum_fecha" id="pn_instrum_fecha" class="datepicker instrumento" readonly />
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
				<h3><?php echo JText::_('LBL_RPP'); ?></h3>
				<div class="form-group">
					<label for="rp_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
					<input type="text" name="rp_instrum_fecha" id="rp_instrum_fecha" class="datepicker instrumento" readonly />
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

				<div class="checkbox">
					<label><input type="checkbox" id="tramiteRegistro"><?php echo JText::_('LBL_EN_TRAMITE'); ?></label>
				</div>
			</div>


		</fieldset>
		<?php
		echo JHtml::_('bootstrap.endSlide');
		echo JHtml::_('bootstrap.endAccordion');
		?>
		<div class="form-actions">
			<button type="button" class="btn btn-primary envio" id="empresa"><?php echo JText::_('LBL_ENVIAR'); ?></button>
		</div>
		<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'banco', JText::_('LBL_TAB_BANCO'));
		?>
		<fieldset id="datosBancarios">
			<div class="form-group">
				<input type="hidden" id="datosBan_id" name="datosBan_id" value="" />
				<label for="db_banco_codigo"><?php echo JText::_('LBL_BANCOS'); ?> *</label>
				<select name="db_banco_codigo" id="db_banco_codigo">
					<option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
					<?php
					echo $optionBancos;
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="db_banco_cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?> *</label>
				<input name="db_banco_cuenta" id="db_banco_cuenta" type="text" maxlength="10" />
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
				<button type="button" class="btn btn-primary" id="agregarBanco"><?php echo JText::_('LBL_CARGAR'); ?></button>
				<button type="button" class="btn btn-primary" id="nextTab"><?php echo JText::_('LBL_NEXTTAB'); ?></button>
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
		</fieldset>
		<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'tabs-clientes', 'files', JText::_('LBL_TAB_ARCHIVOS'));
		?>
		<fieldset>
			<blockquote>
				<p><?php echo JText::sprintf('LBL_FILE_TYPES_ALLOWED', 'JPG, PNG, GIF, y PDF'); ?></p>
				<p><?php echo JText::sprintf('LBL_MAX_FILE_SIZE', '10MB'); ?></p>
			</blockquote>

			<div class="form-group">
				<label for="dp_url_identificacion"><?php echo JText::_('LBL_ID_FILE'); ?> *</label>
				<input name="dp_url_identificacion" type="file" maxlength="" />
			</div>
			<div class="form-group">
				<label for="dp_url_rfc"><?php echo JText::_('LBL_RFC_FILE'); ?> *</label>
				<input name="dp_url_rfc" type="file" maxlength="" />
			</div>

			<div class="form-group">
				<label for="dp_url_comprobante_domicilio"><?php echo JText::_('LBL_COMP_DOMICILIO_FILE'); ?> *</label>
				<input name="dp_url_comprobante_domicilio" type="file" maxlength="" />
			</div>

			<div class="form-group">
				<label for="de_url_rfc"><?php echo JText::_('LBL_RFC_FILE'); ?> *</label>
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
				<button type="submit" class="btn btn-primary envio" id="files"><?php echo JText::_('LBL_ENVIAR'); ?></button>
			</div>
		</fieldset>
		<?php
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');

		echo JHtml::_('form.token');
		?>

	</form>
</div>
<div class="form-actions">
	<a class="btn btn-danger" href="index.php?option=com_mandatos&view=clienteslist"><?php echo JText::_('JCANCEL'); ?></a>
	<a class="btn btn-success disabled" id="btn_fin"><?php echo JText::_('LBL_FIN'); ?></a>
</div>

<?php
echo '<div class="alert alert-dismissable alert-info"><h4>'.JText::_('LBL_FORM_REQUEST_INTEGRADO_INSTRUCTIONS_TITLE').'</h4>'.JText::_('LBL_FORM_REQUEST_INTEGRADO_INSTRUCTIONS').'</div>';
