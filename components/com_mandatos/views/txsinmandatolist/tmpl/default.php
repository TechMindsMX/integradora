<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');


var_dump($this->txs);


?>

<h1><?php echo JText::_('COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE'); ?></h1>

<div class="table-responsive">
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
				$url_preview = JRoute::_('index.php?option=com_mandatos&view=txsinmandato&integradoId='.$this->integradoId.'&facturanum='.$value->id);
				$preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';
				$btn = '<a class="btn btn-success" href="index.php?option=com_mandatos&view=facturapreview&layout=confirmcancel&facturanum='.$value->id.'&integradoId='.$this->integradoId.'">'.JText::_('COM_MANDATOS_ORDENES_CANCEL_FACT').'</a>';

				echo '<tr class="row_'.$value->id.'">';
				echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$value->referencia.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->cuenta.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->date.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->amount,2).'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$btn.'</td>';
				echo '</tr>';
			}
		}else{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MANDATOS_LIST_NO_TXLIST'));
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