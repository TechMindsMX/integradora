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
            'link'			:'?task=searchrfc&format=raw',
            'datos'			:{'rfc': rfcBusqueda, 'integradoId':integradoId}
        };

        var resultado = ajax(envio);

        resultado.done(function(response){
            if(response.success){
                var data = response.datos_personales;
                jQuery('#beneficiario').val(data.nom_comercial);
                jQuery('#integradoIdR').val(data.integrado_id)
            }
        });
    }

    jQuery(document).ready(function(){
        jQuery('#rfc').on('change',searchrfc);
    });
</script>

<h1><?php echo JText::_('COM_MANDATOS_MUTUOS_FORM_TITULO'); ?></h1>

<form id="generaODC" method="post" action="<?php echo JRoute::_('index.php?option=com_mandatos&view=odcform&integradoId=1&confirmacion=1') ?>" role="form" enctype="multipart/form-data">
    <input type="hidden" name="integradoIdE" id="integradoIdE" value="<?php echo $this->integradoId; ?>" />
    <input type="hidden" name="integradoIdR" id="integradoIdR" value="" />
    <input type="hidden" name="" id="" value="" />

    <div class="form-group">
        <label for="rfc"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?></label>
        <input type="text" name="rfc" id="rfc" />
    </div>

    <div class="form-group">
        <label for="beneficiario"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO') ?></label>
        <input type="text" name="beneficiario" id="beneficiario" />
    </div>

    <div class="form-group">
        <label for=""><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_VENCIMIENTO'); ?></label>
        <?php
        $default = date('Y-m-d');
        echo JHTML::_('calendar',$default, 'expirationDate', 'expirationDate', $format = '%Y-%m-%d', $attsCal);
        ?>
    </div>

    <div class="form-group">
        <label for="payments"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTS'); ?></label>
        <input type="text" name="payments" id="payments" />
    </div>

    <div class="form-group">
        <label for="totalAmount"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?></label>
        <input type="text" name="totalAmount" id="totalAmount" />
    </div>

    <div class="form-group">
        <label for="interes"><?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?></label>
        <input type="text" name="interes" id="interes" />
    </div>

    <div class="form-group">
        <input type="button" class="btn btn-primary" id="confirmarodc" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
        <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>