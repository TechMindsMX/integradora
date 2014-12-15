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
<script>
	jQuery(document).ready(function(){
		jQuery('#cancel').on('click', cancelfunction);
	});
	
	function cancelfunction(){
		window.history.back();
	}
</script>
<form id="form_alta" method="post" action="index.php/component/mandatos/?view=txsinmandatosform">
	<input type="hidden" name="idtx" value="<?php echo @$tx->id; ?>" />
	<input type="hidden" name="status" value="<?php echo @$tx->status; ?>" />

	<h1 style="margin-bottom: 40px;"><?php echo JText::_($this->titulo); ?></h1>

	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_REFERENCIA') ?></label>
		<span id="name" value="<?php echo $tx->referencia; ?>" />
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_DATE') ?></label>
		<span id="name" value="<?php echo $tx->date; ?>" />
	</div>
	<div class="form-group">
		<label for="name"><?php echo JText::_('COM_MANDATOS_AMOUNT') ?></label>
		<span id="name" value="<?php echo number_format($tx->amount,2); ?>" />
	</div>


	<h3><?php echo JText::_('COM_MANDATOS_ORDERS'); ?></h3>
	<h4><?php echo JText::_('LBL_ODVS'); ?></h4>
	<div class="table-responsive">
		<table id="myTable" class="table table-bordered tablesorter">

			<thead>
			<tr>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_ORDER_'); ?></span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_ORDER_'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_ORDER_'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_ORDER_'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_ORDER_'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if( !is_null($odvs) ){
				foreach ($odvs as $key => $value) {
					$btn_asoociar = JText::_('COM_MANDATOS_LIST_TX_BTN_ASOCIAR');

					var_dump($value);exit;
					echo '<tr class="row_'.$value->id.'">';
					echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$value->numOrden.'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->createdDate.'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->paymentDate.'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->totalAmount,2).'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->paymentMethod->name.'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->status->name.'</td>';
					echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$btn_asoociar.'</td>';
					echo '</tr>';
				}
			}else{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MANDATOS_LIST_TX_NO_TXLIST'));
			}
			?>
			</tbody>
		</table>
	</div>



	<div class="form-actions">
		<button type="submit" class="btn btn-primary span3" id="send"><?php echo JText::_('LBL_ENVIAR'); ?></button>
        <button type="button" class="btn btn-danger span3" id="cancel"><?php echo JText::_('LBL_CANCELAR'); ?></button>
	</div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary span3" id="empty"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    </div>
</form>

<?php
var_dump($tx, $this->orders);