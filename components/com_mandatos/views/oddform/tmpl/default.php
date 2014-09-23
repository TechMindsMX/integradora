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
<h1><?php echo JText::_('COM_MANDATOS_ODDFORM_TITLE'); ?></h1>
<form id="oddform" action="<?php echo $this->actionUrl; ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="paymentform"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
        <select id="paymentform" name="paymentform">
            <option value="0"><?php echo JText::_('LBL_SPEI'); ?></option>
            <option value="1"><?php echo JText::_('LBL_CHEQUE'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="depositDate"><?php echo JText::_('LBL_DEPOSIT_DATE'); ?></label>
        <?php
        $default = date('Y-m-d');
        echo JHTML::_('calendar',$default, 'depositDate', 'depositDate', $format = '%Y-%m-%d', $attsCal);
        ?>
    </div>

    <div class="form-group">
        <label for="amountDeposited"><?php echo JText::_('LBL_AMOUNT_DEPOSITED'); ?></label>
        <input type="text" name="amountDeposited" id="amountDeposited" />
    </div>

    <div class="form-group">
        <label for="proof"><?php echo JText::_('LBL_PROOF'); ?></label>
        <input type="file" name="proof" id="proof" />
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
        <input type="button" class="btn btn-primary"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>
<?php
}else{
var_dump($this->datos);
?>
<?php
}
?>