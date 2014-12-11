<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = $this->data;
$document = JFactory::getDocument();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
<script>
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
            }else{
                beneficiario.val('');
                beneficiario.prop('readOnly', false);

                jQuery('#integradoIdR').val();
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
        <label for=""><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_VENCIMIENTO'); ?> <span style="font-size: 10px;">Máximo 3 años</span></label>
        <input type="text" name="expirationDate" id="expirationDate" value="<?php echo $datos->expirationDate; ?>" />
    </div>

    <div class="form-group">
        <label for="payments"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTS'); ?></label>
        <select name="payments" id="payments">
            <?php foreach ($this->tipoPago as $key => $val) {
                $selected = $key==$datos->payments?'selected':'';
                ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="totalAmount"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?></label>
        <input type="text" name="totalAmount" id="totalAmount" value="<?php echo $datos->totalAmount; ?>" />
    </div>

    <div class="form-group">
        <label for="interes"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?> <span style="font-size: 10px;">Tasa total del periodo</span></label>
        <input type="text" name="interes" id="interes" value="<?php echo $datos->interes; ?>" />
    </div>

    <div class="form-group">
        <input type="button" class="btn btn-default" id="amortizacion" value="<?php echo jText::_('LBL_AMORTIZACION'); ?>" />
        <input type="submit" class="btn btn-primary" id="confirmarodc" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
        <a href="index.php?option=com_mandatos" class="btn btn-danger" > <?php echo jText::_('LBL_CANCELAR'); ?></a>
    </div>
</form>