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

<?php echo $this->loadTemplate('tx_head'); ?>


<h3><?php echo JText::_('COM_MANDATOS_ORDERS'); ?></h3>

	<?php

	echo showTableOrders($odvs, 'COM_MANDATOS_ODV_LIST', $tx);

	echo showTableOrders($odds, 'COM_MANDATOS_LISTAD_ORDENES_DEPOSITO', $tx);

?>

	<div class="form-actions">
        <a class="btn btn-danger span3" id="cancel" href="index.php?option=com_mandatos&view=txsinmandatolist"><?php echo JText::_('LBL_CANCELAR'); ?></a>
	</div>


<?php
function showTableOrders($orderArray, $tableTitle, $tx){

	$html = '<h4>'. JText::_($tableTitle) .'</h4>
	<div class="table-responsive">
		<table id="myTable" class="table table-bordered tablesorter tableOrders">

			<thead>
			<tr>
				<th><span class="etiqueta">'. JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN') .'</span> </th>
				<th><span class="etiqueta">'. JText::_('LBL_PAYMENT_DATE') .'</span> </th>
				<th><span class="etiqueta">'. JText::_('COM_MANDATOS_LIST_TX_AMOUNT') .' </span> </th>
				<th><span class="etiqueta">'. JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN') .'</span> </th>
				<th><span class="etiqueta">'. JText::_('COM_MANDATOS_ODC_PAYMENTFORM') .' </span> </th>
				<th><span class="etiqueta">'. JText::_('JSTATUS') .'</span> </th>
				<th style="width: 20%;"><span class="etiqueta">'. JText::_('') .'</span> </th>
				<th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			';
			if( !is_null($orderArray) ){
				foreach ($orderArray as $key => $value) {

					$btn_asoociar = JText::_('COM_MANDATOS_ORDERS_MONTO_SUPERIOR_A_TX');

					if($tx->amount > ($value->totalAmount - $value->partialPaymentsTotal)) {
						$url = JRoute::_( 'index.php?option=com_mandatos&view=txsinmandatoform&layout=confirm&txnum='.$tx->id.'&numOrden='.$value->id.'&'. JSession::getFormToken() .'=1&orderType='.$value->orderType);
						$btn_asoociar = '<a class="btn btn-success" id="asociar" href="'.$url.'">'.JText::_('COM_MANDATOS_LIST_TX_BTN_ASOCIAR').'</a>';
					}

					$html .= '<tr class="row_'.$value->id.'">';
					$html .= '	<td>'.$value->createdDate.'</td>';
					$html .= '	<td>'.$value->paymentDate.'</td>';
					$html .= '	<td>$'.number_format($value->totalAmount,2).'</td>';
					$html .= '	<td class="margen-fila" >'.$value->numOrden.'</td>';
					$html .= '	<td>'.$value->paymentMethod->name.'</td>';
					$html .= '	<td>'.$value->status->name.'</td>';
					$html .= '	<td>'.$btn_asoociar.'</td>';
					$html .= '</tr>';
				}
			}else{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MANDATOS_LIST_TX_NO_TXLIST'));
			}

	$html = '</tbody>
		</table>
	</div>
	';

	return $html;
}
