<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
jimport('integradora.notifications');

$datos = @$this->data->integrados;
$user		= JFactory::getUser();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
$optionBancos = '';

echo '<script src="/integradora/libraries/integradora/js/sepomex.js"> </script>';
echo '<script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>';

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

        var tabs = jQuery('#tabs-solicitudTabs li');
        var integradoIdModel = '<?php if (isset($this->data->integrados->integrado->integrado_id)) : echo $this->data->integrados->integrado->integrado_id; endif; ?>';
        if (integradoIdModel == '') {
            tabs.addClass('disabled').find('a').attr("data-toggle", "disabled");
            activeTab( tabs.first() );
        }

        jQuery('form#solicitud button.btn-primary').click(function(){
            var boton = jQuery(this).prop('id');

            if( (boton == 'juridica') || (boton == 'personales') || (boton == 'empresa') || (boton == 'params')){
                var serializado = jQuery('.tab-pane.active :input').serialize();
                datos = serializado
                datos += '&tab='+boton
                if( boton == 'personales' ) {
                    datos += '&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val();
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
                            if(juridica.pj_pers_juridica == 1)
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
        if(!empty($datos->params)){
        ?>
        var params = '<?php echo json_encode($datos->params); ?>';
        var params = eval ("(" + params + ")");

        jQuery.each(params, function(key, value){
            jQuery('#au_'+key).val(value);
        });
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
        jQuery('#agregarBanco').on('click', AltaBanco);
        jQuery('#dp_rfc, #de_rfc').on('change',toUpper);
        jQuery('#tramiteRegistro').on('change', deshabilitaregistroProp)
    });

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
            'link'  : 'index.php?option=com_mandatos&view=clientesform&task=agregarBanco&format=raw',
            'datos' : data
        };

        var resultado = ajax(parametros);

        resultado.done(function(response){
            var obj = response;

            if(obj.success === true) {
                llenatablabancos(obj);
            }else{
                alert('No se pudo agregar la cuenta revisa los datos');
            }
        });

    }

    function bajaBanco(campo){
        var id		 = jQuery(campo).prop('id');
        var idCliPro = jQuery('#integradoId').val();
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
    </script>

    <div class="msgs_plataforma" id="msgs_plataforma"></div>

    <form action="index.php?option=com_integrado&task=uploadFiles" class="form" id="solicitud" name="solicitud" method="post" enctype="multipart/form-data" >
    <input type="hidden" name="user_id" value="<?php echo $this->data->user->id; ?>" />
    <input type="hidden" name="integradoId" id="integradoId" value="<?php echo $this->data->user->integradoId; ?>" />

    <?php
    echo JHtml::_('bootstrap.startTabSet', 'tabs-solicitud', array('active' => 'pers-juridica'));
    echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'pers-juridica', JText::_('COM_INTEG_PERS_JURIDICA'));
    ?>
    <fieldset>
        <div class="radio">
            <label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral1" value="1" <?php echo $moral; ?> /><?php echo JText::_('LBL_PER_MORAL'); ?></label>
        </div>
        <div class="radio">
            <label><input type="radio" name="pj_pers_juridica" id="perFisicaMoral2" value="2" <?php echo $fisica; ?> /><?php echo JText::_('LBL_PER_FISICA'); ?></label>
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
            <label for="dp_nombre_representante"><?php echo JText::_('LBL_NOMBRE_COMPLETO_REPRESENTANTE'); ?></label>
            <input name="dp_nombre_representante" id="dp_nombre_representante" type="text" maxlength="100" value="<?php echo $user->name ?>" />
        </div>
        <div class="form-group">
            <label for="dp_nom_comercial"><?php echo JText::_('LBL_NOM_COMERCIAL'); ?></label>
            <input name="dp_nom_comercial" id="dp_nom_comercial" type="text" maxlength="100" />
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
            <input name="dp_rfc" id="dp_rfc" type="text" maxlength="13" />
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
            <label for="email"><?php echo JText::_('LBL_CORREO'); ?>*</label>
            <input name="dp_email" id="dp_email" type="email" maxlength="100" required />
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
            <input name="de_rfc" id="de_rfc" type="text" maxlength="12" />
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
            <h3><?php echo JText::_('LBL_RPP'); ?></h3>
            <div class="form-group">
                <label for="rp_instrum_fecha"><?php echo JText::_('LBL_FECHA_TESTIMONIO'); ?></label>
                <?php
                echo JHTML::_('calendar',date('Y-m-d'),'rp_instrum_fecha', 'rp_instrum_fecha', $format = '%Y-%m-%d', $attsCal);
                ?>
            </div>
            <div class="form-group">
                <label for="rp_instrum_num_instrumento"><?php echo JText::_('LBL_NUMERO'); ?></label>
                <input name="rp_instrum_num_instrumento" id="rp_instrum_num_instrumento" type="text" maxlength="10" />
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
        <button type="button" class="btn btn-primary span3" id="empresa"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    </div>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'params', JText::_('LBL_TAB_AUTHORIZATIONS'));
    ?>
    <fieldset>
        <div class="form-group">
            <label for="au_params"><?php echo JText::_('LBL_NUM_AUTHORIZATIONS'); ?></label>
            <input type="text" name="au_params" id="au_params" class="au_params" maxlength="2" />
        </div>

    </fieldset>
    <div class="form-actions">
        <button type="button" class="btn btn-primary span3" id="params"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    </div>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'banco', JText::_('LBL_TAB_BANCO'));
    ?>
    <fieldset>
        <div class="form-group">
            <input type="hidden" id="datosBan_id" name="datosBan_id" value="" />
            <label for="db_banco_codigo"><?php echo JText::_('LBL_BANCOS'); ?> *:</label>
            <select name="db_banco_codigo" id="db_banco_codigo">
                <option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
                <?php
                echo $optionBancos;
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="db_banco_cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></label>
            <input name="db_banco_cuenta" id="db_banco_cuenta" type="text" maxlength="10" />
        </div>
        <div class="form-group">
            <label for="db_banco_sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></label>
            <input name="db_banco_sucursal" id="db_banco_sucursal" type="text" maxlength="10" />
        </div>
        <div class="form-group">
            <label for="db_banco_clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></label>
            <input name="db_banco_clabe" id="db_banco_clabe" type="text" maxlength="18" />
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-success span3" id="agregarBanco"><?php echo JText::_('LBL_CARGAR'); ?></button>
            <button type="button" class="btn btn-primary span3" id="nextTab"><?php echo JText::_('LBL_NEXTTAB'); ?></button>
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
    echo JHtml::_('bootstrap.addTab', 'tabs-solicitud', 'files', JText::_('LBL_TAB_ARCHIVOS'));
    ?>
    <fieldset>

        <?php
        $archivos = array(
            'dp_url_identificacion'         => array(@$datos->datos_personales->url_identificacion, 'LBL_ID_FILE'),
            'dp_url_rfc'                    => array(@$datos->datos_personales->url_rfc, 'LBL_RFC_FILE'),
            'dp_url_comprobante_domicilio'  => array(@$datos->datos_personales->url_comprobante_domicilio, 'LBL_COMP_DOMICILIO_FILE'),
            'de_url_rfc'                    => array(@$datos->datos_empresa->url_rfc, 'LBL_RFC_FILE'),
            't1_url_instrumento'            => array(@$datos->testimonio1->url_instrumento, 'LBL_TESTIMONIO1_FILE'),
            't2_url_instrumento'            => array(@$datos->testimonio2->url_instrumento, 'LBL_TESTIMONIO2_FILE'),
            'pn_url_instrumento'            => array(@$datos->poder->url_instrumento, 'LBL_TESTIMONIO3_FILE'),
            'rp_url_instrumento'            => array(@$datos->reg_propiedad->url_instrumento, 'LBL_RPP_FILE'),
            'db_banco_file'                 => array(@$datos->datos_bancarios->banco_file, 'LBL_BANCO_FILE')
        );

        foreach ( $archivos as $key => $value ) {
        ?>
        <div class="form-group">
            <label for="<?php echo $key; ?>" class="head" ><?php echo JText::_($value[1]); ?></label>
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
            <button type="submit" class="btn btn-primary span3" id="files"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        </div>
    </fieldset>
    <?php
    echo JHtml::_('bootstrap.endTab');
    echo JHtml::_('bootstrap.endTabSet');

    ?>

        <input type="hidden" name="<?php echo $token; ?>" value="1" />
    </form>
<?php

?>