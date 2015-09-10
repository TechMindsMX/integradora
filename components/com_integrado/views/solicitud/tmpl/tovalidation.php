
<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 10-Sep-15
 * Time: 10:41 AM
 */

$cancelUrl = JRoute::_('index.php?option=com_integrado');
$finishUrl = $cancelUrl. '&task=finish';

$datos = @$this->data->integrados;

$integrado = new IntegradoSimple($this->data->integrados->integrado->integradoId);

if ($integrado->hasAllDataForValidation()) {
    $msg = JText::_('INFO_ASK_VALIDATION_SOLICITUD');
    $disable =  '';
    $url = $finishUrl;
} else {
    $msg = JText::_('INFO_CANT_ASK_VALIDATION_SOLICITUD');
    $disable =  'disable';
    $url = '';
}

?>

<div class="well bg-info">
    <h2><?php echo $msg; ?></h2>
</div>

<a class="btn btn-success <?php echo $disable; ?>" id="finishBtn" href="<?php echo $url; ?>" ><?php echo JText::_('LBL_FIN'); ?></a>
<a class="btn btn-danger" href="<?php echo $cancelUrl; ?>" ><?php echo JText::_('JCANCEL'); ?></a>
