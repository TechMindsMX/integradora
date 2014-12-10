<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos = $this->datos;
$document = JFactory::getDocument();
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
<script>
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
            'link'			:'index.php?option=com_mandatos&view=mutuosform&task=searchrfc&format=raw',
            'datos'			:{'rfc': rfcBusqueda, 'integradoId':integradoId}
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

    jQuery(document).ready(function(){
        jQuery('#rfc').on('change',searchrfc);
    });
</script>

<h1><?php echo JText::_('COM_MANDATOS_MUTUOS_FORM_TITULO'); ?></h1>

<form id="generaODC" method="post" action="index.php?option=com_mandatos&view=mutuosform&confirmacion=1" role="form" enctype="multipart/form-data">
    <input type="hidden" name="idMutuo" id="idMutuo" value="" />
    <input type="hidden" name="integradoIdE" id="integradoIdE" value="<?php echo $this->integradoId; ?>" />
    <input type="hidden" name="integradoIdR" id="integradoIdR" value="" />

    <div class="form-group">
        <label for="rfc"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?></label>
        <input type="text" name="rfc" id="rfc" />
    </div>

    <div class="form-group">
        <label for="beneficiario"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO') ?></label>
        <input type="text" name="beneficiario" id="beneficiario" />
    </div>

    <div class="form-group">
        <label for=""><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_VENCIMIENTO'); ?> <span style="font-size: 10px;">Máximo 3 años</span></label>
        <input type="text" name="expirationDate" id="expirationDate" />
    </div>

    <div class="form-group">
        <label for="payments"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTS'); ?> <span style="font-size: 10px;">Máximo 2/mes</span></label>
        <select>
            <option value="2">Quincenal</option>
            <option value="3">Mensual</option>
            <option value="4">Bimestral</option>
            <option value="5">Trimestral</option>
            <option value="6">Semestral</option>
            <option value="7">Anual</option>
        </select>
    </div>

    <div class="form-group">
        <label for="totalAmount"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?></label>
        <input type="text" name="totalAmount" id="totalAmount" />
    </div>

    <div class="form-group">
        <label for="interes"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?> <span style="font-size: 10px;">Tasa total del periodo</span></label>
        <input type="text" name="interes" id="interes" />
    </div>

    <div class="form-group">
        <input type="button" class="btn btn-primary" id="confirmarodc" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
        <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>