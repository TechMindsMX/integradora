<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$tx      	= $this->data[0];
$odds       = $this->orders->odd;
$odvs       = $this->orders->odv;
$selected 	= '';
?>

	<h2 style="margin-bottom: 40px;"><?php echo JText::_($this->titulo); ?></h2>
	<div class="form-group">
		<label for="name"><?php echo JText::_('LBL_SOCIO_INTEG') ?></label>
		<h3><?php echo $this->integrado->displayName; ?></h3>
	</div>

<div style="background-color: #eeeeee; padding: 2em;">
	<h3><?php echo JText::_('COM_MANDATOS_LIST_TX_DATA'); ?></h3>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_REF') ?></label>
		<span id="name"><?php echo $tx->referencia; ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_DATE') ?></label>
		<span id="name"><?php echo $tx->date; ?></span>
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT') ?></label>
		<span id="name"><?php echo number_format($tx->amount,2); ?></span>
	</div>
</div>

	<h3><?php echo JText::_('COM_MANDATOS_ORDERS'); ?></h3>

	<?php

	echo showTableOrders($odvs, 'COM_MANDATOS_ODV_LIST', $tx->id, $this->data[0]->integradoId);

	echo showTableOrders($odds, 'COM_MANDATOS_LISTAD_ORDENES_DEPOSITO', $tx->id, $this->data[0]->integradoId);

?>

	<div class="form-actions">
        <a class="btn btn-danger span3" id="cancel" href="index.php?option=com_mandatos&view=txsinmandatolist"><?php echo JText::_('LBL_CANCELAR'); ?></a>
	</div>


<?php
function showTableOrders($orderArray, $tableTitle, $txId, $integId){
	?>
	<h4><?php echo JText::_($tableTitle); ?></h4>
	<div class="table-responsive">
		<table id="myTable" class="table table-bordered tablesorter tableOrders">

			<thead>
			<tr>
				<th><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?></span> </th>
				<th><span class="etiqueta"><?php echo JText::_('LBL_PAYMENT_DATE'); ?> </span> </th>
				<th><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT'); ?> </span> </th>
				<th><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN'); ?> </span> </th>
				<th><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?> </span> </th>
				<th><span class="etiqueta"><?php echo JText::_('LBL_ORDER_STATUS'); ?> </span> </th>
				<th><span class="etiqueta"><?php echo JText::_(''); ?> </span> </th>
				<th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if( !is_null($orderArray) ){
				foreach ($orderArray as $key => $value) {
					$url = JRoute::_( 'index.php?option=com_mandatos&view=txsinmandatoform&layout=confirm&txnum='.$txId.'&numOrden='.$value->id.'&'. JSession::getFormToken() .'=1&integradoId='.$integId);
					$btn_asoociar = '<a class="btn btn-success" href="'.$url.'">'.JText::_('COM_MANDATOS_LIST_TX_BTN_ASOCIAR');

					echo '<tr class="row_'.$value->id.'">';
					echo '	<td>'.$value->createdDate.'</td>';
					echo '	<td>'.$value->paymentDate.'</td>';
					echo '	<td>$'.number_format($value->totalAmount,2).'</td>';
					echo '	<td class="margen-fila" >'.$value->numOrden.'</td>';
					echo '	<td>'.$value->paymentMethod->name.'</td>';
					echo '	<td>'.$value->status->name.'</td>';
					echo '	<td>'.$btn_asoociar.'</td>';
					echo '</tr>';
				}
			}else{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MANDATOS_LIST_TX_NO_TXLIST'));
			}
			?>
			</tbody>
		</table>
	</div>
<?php
}


var_dump($this);