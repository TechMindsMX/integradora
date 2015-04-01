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

<form action="<?php echo JRoute::_('index.php?option=com_integrado&view=integradoparams&layout=edit&id='.(int)$this -> item -> id); ?>" method="post" name="adminForm" id="adminForm">
    <div class="form-vertical">
        <legend><?php echo JText::_('COM_INTEGRADO_MANAGER_PARAMETRIZACION').' - '.$nombre; ?></legend>

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