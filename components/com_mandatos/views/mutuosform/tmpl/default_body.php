<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos        = $this->data;
$document     = JFactory::getDocument();
$attsCal      = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$optionBancos = '';
?>

<script>
    var catalogoBancos = new Array();
    var integradoId	= <?php echo $this->integradoId; ?>;

    <?php
    foreach ($this->catalogos->bancos as $key => $value){
        $selected = $datos->banco_codigo == $value->claveClabe?'selected="selected"':'';
        $optionBancos .= '<option value="'.$value->claveClabe.'" '.$selected.'>'.$value->banco.'</option>';
        echo 'catalogoBancos["'.$value->claveClabe.'"] = "'.$value->banco.'";'." \n";
	}
    ?>

    jQuery(document).ready(function(){
        jQuery('#rfc').on('change',searchrfc);
        jQuery('#amortizacion').on('click',getTable);

        <?php echo !is_null($datos->rfc)?"jQuery('#rfc').trigger('change');":""; ?>
    });

    function ajax(parametros){

        var request = jQuery.ajax({
            url: parametros.link,
            data: parametros.datos,
            type: 'post'
        });

        return request;
    }

    function searchrfc() {
        var rfcBusqueda	=  jQuery('#rfc').val();
        var integradoId =  jQuery('#integradoIdE').val();

        var envio = {
            'link'	:'index.php?option=com_mandatos&view=mutuosform&task=searchrfc&format=raw',
            'datos'	:{'rfc': rfcBusqueda, 'integradoId':integradoId}
        };

        var resultado = ajax(envio);

        resultado.done(function(response){
            var beneficiario = jQuery('#beneficiario');
            if(response.success){
                var data = response.datos_personales;

                beneficiario.val(data.nom_comercial);
                beneficiario.prop('readOnly', true);

                jQuery('#integradoIdR').val(data.integrado_id);
                jQuery('#dataBanco').hide();
            }else{
                beneficiario.val('');
                beneficiario.prop('readOnly', false);

                jQuery('#integradoIdR').val('');
                jQuery('#dataBanco').show();
            }
        });
    }

    function getTable() {
        var vencimiento = jQuery('#expirationDate').val();
        var tipoPlazo   = jQuery('#payments').val();
        var capital     = jQuery('#totalAmount').val();
        var interes     = jQuery('#interes').val();

        var parametros = {
            'link'  : 'index.php?option=com_mandatos&view=mutuosform&task=tabla&format=raw',
            'datos' : {
                'tiempoplazo' : vencimiento,
                'tipoPlazo'   : tipoPlazo,
                'capital'     : capital,
                'interes'     : interes
            }
        };
        var request = ajax(parametros);

    }
</script>

<h1><?php echo JText::_('COM_MANDATOS_MUTUOS_FORM_TITULO'); ?></h1>
<div class="clearfix">&nbsp;</div>


<form id="generaODC" method="post" action="index.php?option=com_mandatos&view=mutuosform&layout=confirmMutuo" role="form" enctype="multipart/form-data">
    <input type="hidden" name="idMutuo" id="idMutuo" value="<?php $datos->idMutuo; ?>" />
    <input type="hidden" name="integradoId" id="integradoId" value="<?php echo $this->integradoId; ?>" />
    <input type="hidden" name="integradoIdR" id="integradoIdR" value="<?php $datos->integradoIdR; ?>" />

    <div class="form-group">
        <label for="rfc"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?></label>
        <input type="text" name="rfc" id="rfc" value="<?php echo $datos->rfc; ?>" />
    </div>

    <div class="form-group">
        <label for="beneficiario"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO') ?></label>
        <input type="text" name="beneficiario" id="beneficiario" value="<?php echo $datos->beneficiario; ?>" />
    </div>

    <div class="form-group">
        <label for="paymentPeriod"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTPERIOD'); ?></label>
        <select name="paymentPeriod" id="paymentPeriod">
            <?php foreach ($this->tipoPago as $key => $val) {
                $selected = $key==$datos->paymentPeriod?'selected="selected"':'';
                ?>
                <option value="<?php echo $key; ?>" <?php echo $selected;?> ><?php echo $val; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="quantityPayments"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_QUANTITYPAYMENTS'); ?> <span style="font-size: 10px;">Máximo 3 años</span></label>
        <input type="text" name="quantityPayments" id="quantityPayments" value="<?php echo $datos->quantityPayments; ?>" />
    </div>

    <div class="form-group">
        <label for="totalAmount"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?></label>
        <input type="text" name="totalAmount" id="totalAmount" value="<?php echo $datos->totalAmount; ?>" />
    </div>

    <div class="form-group">
        <label for="interes"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?> <span style="font-size: 10px;">Tasa total del periodo</span></label>
        <input type="text" name="interes" id="interes" value="<?php echo $datos->interes; ?>" />
    </div>

    <div id="dataBanco" style="display: none">
        <span style="font-size: 12px;">Es necesario llenar los datos bancarios.</span>
        <div class="form-group">
            <input type="hidden" id="datosBan_id" name="datosBan_id" value="" />

            <label for="banco_codigo"><?php echo JText::_('LBL_BANCOS'); ?> *:</label>
            <select name="banco_codigo" id="banco_codigo">
                <option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
                <?php
                echo $optionBancos;
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="banco_cuenta"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></label>
            <input name="banco_cuenta" id="banco_cuenta" type="text" maxlength="10" value="<?php echo $datos->banco_cuenta ?>" />
        </div>
        <div class="form-group">
            <label for="banco_sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></label>
            <input name="banco_sucursal" id="banco_sucursal" type="text" maxlength="3" value="<?php echo $datos->banco_sucursal ?>" />
        </div>
        <div class="form-group">
            <label for="banco_clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></label>
            <input name="banco_clabe" id="banco_clabe" type="text" maxlength="18" value="<?php echo $datos->banco_clabe ?>" />
        </div>
    </div>

    <div class="form-group">
        <input type="button" class="btn btn-default" id="amortizacion" value="<?php echo jText::_('LBL_AMORTIZACION'); ?>" />
        <input type="submit" class="btn btn-primary" id="confirmarodc" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
        <a href="index.php?option=com_mandatos" class="btn btn-danger" > <?php echo jText::_('LBL_CANCELAR'); ?></a>
    </div>
</form>