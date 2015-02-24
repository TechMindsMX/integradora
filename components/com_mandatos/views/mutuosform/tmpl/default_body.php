<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos        = $this->data;
$datosBanco   = isset($datos->integradoDeudor->datosBancarios[0])?$datos->integradoDeudor->datosBancarios[0]:null;
$document     = JFactory::getDocument();
$attsCal      = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
$optionBancos = '';
?>
<script src="libraries/integradora/js/tim-validation.js"> </script>
<script>
    var catalogoBancos = new Array();
    var integradoId	= <?php echo $this->integradoId; ?>;

    <?php
    foreach ($this->catalogos->bancos as $key => $value){
        $bancoCodigo = isset($datosBanco->banco_codigo)?$datosBanco->banco_codigo:null;

        $selected = $bancoCodigo == $value->claveClabe?'selected="selected"':'';
        $optionBancos .= '<option value="'.$value->claveClabe.'" '.$selected.'>'.$value->banco.'</option>';
        echo 'catalogoBancos["'.$value->claveClabe.'"] = "'.$value->banco.'";'." \n";
	}
    ?>

    jQuery(document).ready(function(){
        jQuery('#rfc').on('change',searchrfc);
        jQuery('#amortizacion').on('click',getTable);

        <?php echo (!is_null($datos->integradoDeudor->rfc)&& !isset($datos->id))?"jQuery('#rfc').trigger('change');":""; ?>
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
            'link'	:'index.php?option=com_integrado&task=search_rfc_cliente&format=raw',
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
        var vencimiento = jQuery('#quantityPayments').val();
        var tipoPlazo   = jQuery('#paymentPeriod').val();
        var capital     = jQuery('#totalAmount').val();
        var interes     = jQuery('#interes').val();

        var parametros = {
            'link'  : 'index.php?option=com_mandatos&view=mutuosform&task=tabla&format=raw',
            'datos' : {
                'quantityPayments' : vencimiento,
                'paymentPeriod'   : tipoPlazo,
                'totalAmount'     : capital,
                'interes'     : interes
            }
        };
        var request = ajax(parametros);

        request.done(function(response){

            if(typeof response.amortizacion_capital_fijo != 'undefined') {
                var button = jQuery('#amortizacion');
                var tableCapitalFijo = '';
                var tableCuotaFija = '';
                var totalInteresCAF = 0;
                var totalIVACAF = 0;
                var totalInteresCUF = 0;
                var totalIVACUF = 0;

                jQuery.each(response.amortizacion_capital_fijo, function (k, v) {
                    tableCapitalFijo += '<tr class="row">';
                    tableCapitalFijo += '<td>' + v.periodo + '</td>';
                    tableCapitalFijo += '<td>$' + v.inicial.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.cuota.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.intiva.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.intereses.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.iva.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.acapital.toFixed(2) + '</td>';
                    tableCapitalFijo += '<td>$' + v.final.toFixed(2) + '</td>';
                    tableCapitalFijo += '</tr>';

                    totalInteresCAF = totalInteresCAF + v.intereses;
                    totalIVACAF = totalIVACAF + v.iva;
                });

                jQuery.each(response.amortizacion_cuota_fija, function (key, value) {
                    tableCuotaFija += '<tr class="row">';
                    tableCuotaFija += '<td>' + value.periodo + '</td>';
                    tableCuotaFija += '<td>$' + value.inicial.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.cuota.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.intiva.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.intereses.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.iva.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.acapital.toFixed(2) + '</td>';
                    tableCuotaFija += '<td>$' + value.final.toFixed(2) + '</td>';
                    tableCuotaFija += '</tr>';

                    totalInteresCUF = totalInteresCUF + value.intereses;
                    totalIVACUF = totalIVACUF + value.iva;
                });

                jQuery('#tablaCapitalCAF').html('$' + response.capital.toFixed(2));
                jQuery('#totalInteresCAF').html('$' + totalInteresCAF.toFixed(2));
                jQuery('#totalIVACAF').html('$' + totalIVACAF.toFixed(2));

                jQuery('#tablaCapitalCUF').html('$' + response.capital.toFixed(2));
                jQuery('#totalInteresCUF').html('$' + totalInteresCUF.toFixed(2));
                jQuery('#totalIVACUF').html('$' + totalIVACUF.toFixed(2));

                jQuery('.tablaAmortizacionCAF').html(tableCapitalFijo);
                jQuery('.tablaAmortizacionCUF').html(tableCuotaFija);

                jQuery('#tables').show();
                button.remove();

                var buttonenviar = jQuery('#botones_envio');
                buttonenviar.html('<input class="btn btn-primary" id="confirmarodc" type="submit" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />');
            }else{
                mensajesValidaciones(response);
            }
        });

    }
</script>

<h1><?php echo JText::_('COM_MANDATOS_MUTUOS_FORM_TITULO'); ?></h1>
<div>
    <div class="span6"><h4>MONTO EN PRESTAMOS: $<?php echo number_format($this->montoSaldo->montoPrestamos,2); ?></h4></div>
    <div class="span6"><h4>SALDO EN CUENTA: $<?php echo number_format($this->montoSaldo->saldoDisponible,2); ?></h4></div>
</div>
<div class="clearfix">&nbsp;</div>
<form id="generaODC" method="post" action="index.php?option=com_mandatos&view=mutuosform&layout=confirm" role="form" enctype="multipart/form-data">
    <div>
        <input type="hidden" name="id" id="id" value="<?php echo $datos->id; ?>" />
        <input type="hidden" name="integradoId" id="integradoId" value="<?php echo $this->integradoId; ?>" />
        <input type="hidden" name="integradoIdR" id="integradoIdR" value="<?php echo $datos->integradoIdR; ?>" />
        <input type="hidden" name="jsonTabla" id="jsonTabla" />

        <div class="form-group">
            <label for="rfc"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?></label>
            <input type="text" name="rfc" id="rfc" value="<?php echo $datos->integradoDeudor->rfc; ?>" />
        </div>

        <div class="form-group">
            <label for="beneficiario"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO') ?></label>
            <input type="text" name="beneficiario" id="beneficiario" value="<?php echo isset($datos->integradoDeudor)?$datos->integradoDeudor->nombre:''; ?>" />
        </div>

        <div class="form-group">
            <label for="paymentPeriod"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTPERIOD'); ?></label>
            <select name="paymentPeriod" id="paymentPeriod">
                <?php foreach ($this->tipoPago as $key => $val) {
                    $selected = $key==$datos->paymentPeriod?'selected="selected"':'';
                    ?>
                    <option value="<?php echo $key; ?>" <?php echo $selected;?> ><?php echo $val->nombre; ?></option>
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

        <div id="dataBanco" style="display: <?php echo isset($datos_banco->banco_codigo)?'show':'none'; ?>">
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
                <input name="banco_cuenta" id="banco_cuenta" type="text" maxlength="10" value="<?php echo isset($datos_banco->banco_cuenta)?$datos_banco->banco_cuenta:'' ?>" />
            </div>
            <div class="form-group">
                <label for="banco_sucursal"><?php echo JText::_('LBL_BANCO_SUCURSAL'); ?></label>
                <input name="banco_sucursal" id="banco_sucursal" type="text" maxlength="3" value="<?php echo isset($datos_banco->sucursal)?$datos_banco->sucursal:'' ?>" />
            </div>
            <div class="form-group">
                <label for="banco_clabe"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></label>
                <input name="banco_clabe" id="banco_clabe" type="text" maxlength="18" value="<?php echo isset($datos_banco->banco_clabe)?$datos_banco->banco_clabe:'' ?>" />
            </div>
        </div>

        <div class="form-group">
            <span id="botones_envio"><input type="button" class="btn btn-default" id="amortizacion" value="<?php echo jText::_('LBL_AMORTIZACION'); ?>" /></span>

            <a href="index.php?option=com_mandatos&view=mutuoslist&integradoId" class="btn btn-danger" > <?php echo jText::_('LBL_CANCELAR'); ?></a>
        </div>
    </div>

    <h4>Seleccione la opcion de pago: </h4>
    <div style="display: none" id="tables">
        <div class="span6">
            <h2>Capital Fijo <input type="radio" value="0" checked name="cuotaOcapital" /> </h2>
            <div>
                <div class="span4">Capital: <span id="tablaCapitalCAF"></span></div>
                <div class="span4">Total Interes: <span id="totalInteresCAF"></span></div>
                <div class="span4">Total Iva: <span id="totalIVACAF"></span></div>

            </div>
            <table class="table table-bordered" style="100%; text-align: center;">
                <thead>
                <tr class="row">
                    <th>Periodo</th>
                    <th>Saldo Inicial</th>
                    <th>Couta</th>
                    <th>Interes con IVA</th>
                    <th>Interes</th>
                    <th>IVA</th>
                    <th>Abono a Capital</th>
                    <th>Saldo Final</th>
                </tr>
                </thead>
                <tbody class="tablaAmortizacionCAF">

                </tbody>
            </table>
        </div>
        <div class="span6">
            <h2>Cuota Fija <input type="radio" value="1" name="cuotaOcapital" /></h2>
            <div>
                <div class="span4">Capital: <span id="tablaCapitalCUF"></span></div>
                <div class="span4">Total Interes: <span id="totalInteresCUF"></span></div>
                <div class="span4">Total Iva: <span id="totalIVACUF"></span></div>

            </div>
            <table class="table table-bordered" style="width: 100%; text-align: center;">
                <thead>
                <tr class="row">
                    <th>Periodo</th>
                    <th>Saldo Inicial</th>
                    <th>Couta</th>
                    <th>Interes con IVA</th>
                    <th>Interes</th>
                    <th>IVA</th>
                    <th>Abono a Capital</th>
                    <th>Saldo Final</th>
                </tr>
                </thead>
                <tbody class="tablaAmortizacionCUF">

                </tbody>
            </table>
        </div>
    </div>
</form>