<?php
defined('_JEXEC') or die('Restricted Access');
?> 

<?
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_integrado'); ?>" method="post" name="adminForm">
<table class="adminlist">
 	<thead>
		<tr>
			<th width="20">
			<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_NAME'); ?>
			</th>
			<th width="5">
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_ID'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->items as $i => $item): ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td>
			<?php echo JHtml::_('grid.id', $i, $item -> id); ?>
			</td>
			<td>
			<?php echo $item -> id; ?>
			</td>
			<td>
			<?php echo $item -> status; ?>
			</td>
			<td>
			<?php echo $item -> pers_juridica; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="3"><?php echo $this -> pagination -> getListFooter(); ?></td>
		</tr>
	</tfoot>
</table>

</form>