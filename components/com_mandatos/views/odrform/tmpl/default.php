<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

if(!$this->confirmacion){
?>
<script>
    jQuery(document).ready(function(){
        jQuery('#amountRequested').on('change', validaSaldo);
    });

    function validaSaldo(){
        var monto           = jQuery(this).val();
        var saldo           = jQuery('#balance').val();
        var errormsg        = jQuery('#errormsg');
        var fielderrormsg   = jQuery('#amountRequested');

        errormsg.text('<?php echo JText::_('COM_MANDATOS_ORDEN_RETIRO_AMOUNT_ERROR'); ?>');
        errormsg.fadeIn();
        fielderrormsg.css('border-color', '#FF0000');

        if(saldo > monto){
            errormsg.delay(800).fadeOut(4000, function(){
                fielderrormsg.css('border-color', '');
                errormsg.text('');
            });
        }
    }
</script>
<h1><?php echo JText::_('COM_MANDATOS_ODDFORM_TITLE'); ?></h1>
<div style="margin-bottom: 10px;">
    <span class="label-default"><?php echo JText::_('COM_MANDATOS_ORDEN_RETIRO_BALANCE'); ?></span>
    <span>$<?php echo number_format($this->balance); ?></span>
</div>

<form id="oddform" action="<?php echo $this->actionUrl; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" id="balance" value="<?php echo $this->balance; ?>">

    <div class="form-group">
        <label for="paymentform"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
        <select id="paymentform" name="paymentform">
            <option value="0"><?php echo JText::_('LBL_SPEI'); ?></option>
            <option value="1"><?php echo JText::_('LBL_CHEQUE'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="amountRequested"><?php echo JText::_('LBL_AMOUNT_REQUESTED'); ?></label>
        <input type="text" name="amountRequested" id="amountRequested" /> <span id="errormsg" style="display: none;"></span>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
        <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>
<?php
}else{
    $datos = $this->datos;
    $archivo = $this->file;
    $formadepago = array( JText::_('LBL_SPEI'), JText::_('LBL_CHEQUE') );
?>
    <h1><?php echo JText::_('COM_MANDATOS_ORDENES_DEPOSITO_LBL_CONFIMACION'); ?></h1>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_FORMA_PAGO'); ?>: </span>
        <span>
            <?php echo $formadepago[$datos['paymentform']]; ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_DEPOSIT_DATE'); ?>: </span>
        <span>
            <?php echo $datos['depositDate'] ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_AMOUNT_DEPOSITED'); ?>: </span>
        <span>
            $<?php echo number_format($datos['amountDeposited'],2 ); ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_ONLY_PROOF'); ?>: </span>
        <span>
            <a href="<?php echo JRoute::_($archivo['ruta']); ?>"><?php echo $archivo['name']; ?></a>
        </span>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="button" class="btn btn-primary" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
        <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
<?php
}
?>