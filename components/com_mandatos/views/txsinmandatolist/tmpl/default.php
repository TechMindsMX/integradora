<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$btn_nuevo = '<a class="btn btn-primary" href="index.php?option=com_mandatos&integradoId='.$this->integradoId.'">'.JText::_('COM_MANDATOS_LIST_TX_BTN_NUEVO_MANDATO').'</a>';

?>

<h1><?php echo JText::_('COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE'); ?></h1>

<div class="table-responsive">
	<div class="form-group">
		<?php echo $btn_nuevo; ?>
	</div>
	<table id="myTable" class="table table-bordered tablesorter">

		<thead>
		<tr>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_REF'); ?></span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_ACCOUNT'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_DATE'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if( !is_null($this->txs) ){
			foreach ($this->txs as $key => $value) {
				$btn_asoociar = '<a class="btn btn-success" href="index.php?option=com_mandatos&view=txsinmandato&layout=confirml&txnum='.$value->id.'&integradoId='.$this->integradoId.'">'.JText::_('COM_MANDATOS_LIST_TX_BTN_ASOCIAR').'</a>';

				echo '<tr class="row_'.$value->id.'">';
				echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$value->referencia.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->cuenta.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->date.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->amount,2).'</td>';
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

<div style="margin-top: 20px;">
	<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&integradoId='.$this->integradoId); ?>" />
	<?php echo JText::_('COM_MANDATOS_TITULO'); ?>
	</a>
</div>