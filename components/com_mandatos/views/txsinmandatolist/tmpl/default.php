<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$btn_nuevo_odv = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=odvform">'.JText::_('COM_MANDATOS_LIST_TX_BTN_NUEVO_MANDATO').' '.JText::_('ODV_DESCRIPTION').'</a>';
$btn_nuevo_odd = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=oddform">'.JText::_('COM_MANDATOS_LIST_TX_BTN_NUEVO_MANDATO').' '.JText::_('ODD_DESCRIPTION').'</a>';

?>

<h1><?php echo JText::_('COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE'); ?></h1>

<div class="table-responsive">
	<table id="myTable" class="table table-bordered tablesorter">

		<thead>
		<tr>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_REF'); ?></span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_DATE'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LIST_TX_AMOUNT'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_BALANCE'); ?> </span> </th>
			<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta">Asociación a Mandatos</span> </th>
		</tr>
		</thead>
		<tbody>
		<?php
		if( !is_null($this->txs) ){
			foreach ($this->txs as $key => $value) {
				$btn_asoociar = '<a class="btn btn-success" href="index.php?option=com_mandatos&view=txsinmandatoform&txnum='.$value->id.'">'.JText::_('COM_MANDATOS_LIST_TX_BTN_ASOCIAR').'</a>';

				echo '<tr class="row_'.$value->id.'">';
				echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$value->idTx.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.date('d-m-Y', $value->date).'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->details->amount,2).'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->balance,2).'</td>';
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
	<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>">
		<?php echo JText::_('COM_MANDATOS_TITULO'); ?>
	</a>
</div>

<div class="control-group form-actions">
	<p>
		<?php echo JText::_('COM_MANDATOS_TX_SIN_MANDATO_LIST_LEGEND');?>
	</p>
	<span>
		<?php echo $btn_nuevo_odv; ?>
	</span>
	<span>
		<?php echo $btn_nuevo_odd; ?>
	</span>
</div>
