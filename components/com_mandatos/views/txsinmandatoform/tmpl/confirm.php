<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 15-Dec-14
 * Time: 11:43 AM
 */
defined('_JEXEC') or die('Restricted access');

// TODO: Ingresar el dato en el modelo
$integradoId = JFactory::getApplication()->input->get('integradoId');

$accion = 'index.php?com_mandatos&view=txsinmandatoform&task=asociatxmandato.save';
$cancelUrl = 'index.php?com_mandatos&view=txsinmandatolist';


//var_dump($this);exit;
echo $this->loadTemplate('tx_head');
?>

<div style="background-color: #eeeeee; padding: 2em;">
	<h3><?php echo JText::_('COM_MANDATOS_LIST_ORDER_DATA'); ?></h3>
<div class="form-group">
	<label for="name"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN') ?></label>
	<span id="name"><?php echo $this->orders->numOrden; ?></span>
</div>
<div class="form-group">
	<label for="name"><?php echo JText::_('LBL_PAYMENT_DATE') ?></label>
	<span id="name"><?php echo $this->orders->createdDate; ?></span>
</div>
<div class="form-group">
	<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT') ?></label>
	<span id="name"><?php echo number_format($this->orders->totalAmount,2); ?></span>
</div>
<div class="form-group">
	<label for="name"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM') ?></label>
	<span id="name"><?php echo $this->orders->paymentMethod->name; ?></span>
</div>
<div class="form-group">
	<label for="name"><?php echo JText::_('LBL_ORDER_STATUS') ?></label>
	<span id="name"><?php echo $this->orders->status->name; ?></span>
</div>
</div>

<form action="<?php echo $accion; ?>" method="post" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="numOrden" value="<?php echo $this->orders->id; ?>">
	<input type="hidden" name="orderType" value="<?php echo $this->orders->orderType; ?>">
	<input type="hidden" name="idTx" value="<?php echo $this->data[0]->id; ?>">
	<input type="hidden" name="integradoId" value="<?php echo $integradoId; ?>">
	<?php echo JHtml::_('form.token'); ?>

	<div class="form-actions">
		<input class="btn btn-success span3" type="submit" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
		<a class="btn btn-danger span3" href="<?php echo $cancelUrl; ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
	</div>
</form>