<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
jimport('joomla.html.html.bootstrap');

$integ              = $this->item->integrados[0];
$comisiones         = $this->item->comisiones;
$nombre             = (isset($integ->datos_empresa->razon_social)) ? $integ->datos_empresa->razon_social : $this->item->usuarios[0]->name;
$idComisionSelected = array();
$exist              = false;

if(!empty($this->item->dataSaved)){
    $dataSaved = $this->item->dataSaved;
    $exist     = true;

    foreach ($dataSaved['comisiones'] as $comision) {
        $idComisionSelected[] = $comision->id;
    }
}
?>
<script src="../libraries/integradora/js/tim-validation.js"> </script>

<script>
    function valida() {
        var data = jQuery('#params').val();
        var loading = jQuery('#loading');

        var parametros = {
            'link': 'index.php?option=com_adminintegradora&task=validacionforms.validaparams&format=raw',
            'datos': {'params':data}
        };

        loading.show();
        var request = jQuery.ajax({
            url: parametros.link,
            data: parametros.datos,
            type: 'post'
        });

        request.done(function (response) {
            loading.hide()
            if (response.success) {
                Joomla.submitbutton('integradoparams.save');
            } else {
                mensajesValidaciones(response);
            }
        });
    }

    jQuery(Document).ready(function(){
        var btnSave = jQuery('#toolbar-save').find('button');
        btnSave.removeProp('onclick');

        btnSave.on('click', valida);
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_integrado&view=integradoparams&layout=edit&id='.(STRING)$this -> item -> id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <div class="form-vertical">
        <legend><?php echo JText::_('COM_INTEGRADO_MANAGER_PARAMETRIZACION').' - '.$nombre; ?> <span id="loading" style="display: none;"><img style="height: 33px;" src="../media/media/images/loading.gif" </span> </legend>

        <div class="input-group">
            <label for="params">Número de Autorizaciones</label>
            <input type="text" name="params" id="params" value="<?php echo isset($dataSaved)?$dataSaved['params']:'';?>" >
        </div>

        <div class="input-group">
            <label>Comisiones</label>

            <?php
            foreach ($comisiones as $comision) {
                $selected = '';
                if( in_array($comision->id,$idComisionSelected) ){
                    $selected = 'checked="checked"';
                }
            ?>
                <div class="span12">
                    <div class="span2">
                        <input type="checkbox" class="input-group" name="comision[]" value="<?php echo $comision->id.'" '.$selected; ?>>
                        <?php echo $comision->description; ?>
                    </div>
                    <div class="span10"><?php echo $comision->rate; ?>%</div>
                </div>
            <?php } ?>
        </div>
    </div>
    <input type="hidden" name="exist" value="<?php echo $exist;?>">
    <input type="hidden" name="task" value="integrado.save" />
    <?php echo JHtml::_('form.token'); ?>
</form>