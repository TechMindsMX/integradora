<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$datos = $this->data;
?>
<script>
    function cancel() {
        var form = jQuery('#confirmForm');
        form.prop('action', 'index.php?option=com_mandatos&view=mutuosform');
        form.submit();
    }

    function confirm(){
        var form = jQuery('#confirmForm');
        form.prop('action', 'index.php?option=com_mandatos&view=mutuospreview');

        //form.submit();
    }

    jQuery(document).ready(function(){
        jQuery('#cancel').on('click',cancel);
        jQuery('#confirm').on('click',confirm);
    });
</script>

<h2><?php echo JText::_($this->titulo); ?></h2>

<p>Esta seguro de crear Mutuo con los siguientes Datos.</p>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_RFC'); ?>: </div>
<div><strong><?php echo $datos->rfc; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_BENEFICIARIO'); ?>: </div>
<div><strong><?php echo $datos->beneficiario; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_VENCIMIENTO'); ?>: </div>
<div><strong><?php echo $datos->expirationDate; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_PAYMENTS'); ?>: </div>
<div><strong><?php echo $this->tipoPago[$datos->payments]; ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_TOTALAMOUNT'); ?>: </div>
<div><strong>$<?php echo number_format($datos->totalAmount,2); ?></strong></div>
<div class="clearfix">&nbsp;</div>

<div>
    <?php echo JText::_('COM_MANDATOS_MUTUOS_LBL_INTERES'); ?>: </div>
<div><strong><?php echo $datos->interes; ?>%</strong></div>
<div class="clearfix">&nbsp;</div>

<form id="confirmForm" method="post" action="">
<input type="hidden" id="idMutuo"        name="idMutuo"        value="<?php echo $datos->idMutuo; ?>" />
<input type="hidden" id="integradoId"    name="integradoId"    value="<?php echo $datos->integradoId; ?>" />
<input type="hidden" id="integradoIdR"   name="integradoIdR"   value="<?php echo $datos->integradoIdR; ?>" />
    <input type="hidden" id="rfc"            name="rfc"            value="<?php echo $datos->rfc; ?>" />
    <input type="hidden" id="integradoIdR"   name="integradoIdR"   value="<?php echo $datos->beneficiario; ?>" />
<input type="hidden" id="expirationDate" name="expirationDate" value="<?php echo $datos->expirationDate; ?>" />
<input type="hidden" id="payments"       name="payments"       value="<?php echo $datos->payments; ?>" />
<input type="hidden" id="totalAmount"    name="totalAmount"    value="<?php echo $datos->totalAmount; ?>" />
<input type="hidden" id="interes"        name="interes"        value="<?php echo $datos->interes; ?>" />


    <div class="row">
        <div class="span3">
            <input type="button" class="btn btn-primary" id="confirm" value="Confirmar" />
            <input type="button" class="btn btn-danger"  id="cancel" value="Cancelar" />
        </div>
    </div>
</form>