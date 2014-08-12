<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
?>

<script language="javascript" type="text/javascript">
	function tableOrdering(order, dir, task) {
		var form = document.adminForm;

		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		document.adminForm.submit(task);
	}
	jQuery(document).ready(function() {
		jQuery("input:checkbox").click(function(){
	        var group = "input:checkbox[name='"+jQuery(this).prop("name")+"']";
	        jQuery(group).not(this).prop("checked",false);
    	});
	});
</script>


<form action="<?php echo JRoute::_('index.php?option=com_integrado'); ?>" method="post" name="adminForm" id="adminForm">

<table class="adminlist table">
 	<thead>
		<tr>
			<th width="20">
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_INTEGRADO_INTEGRADO_HEADING_ID'), 'a.integrado_id', $this -> sortDirection, $this -> sortColumn); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_R_SOCIAL'); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_CONTACTO'); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_INTEGRADO_INTEGRADO_HEADING_STATUS'), 'a.status', $this -> sortDirection, $this -> sortColumn); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_PERS_JURIDICA'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->items as $i => $item): ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td>
			<?php echo JHtml::_('grid.id', $i, $item -> integrado_id); ?>
			</td>
			<td>
			<?php echo $item -> integrado_id; ?>
			</td>
			<td>
			<?php $nombre = ($item -> razon_social) ? $item -> razon_social : $item -> name;
				echo $nombre;
 ?>
			</td>
			<td>
			<?php echo $item -> name; ?>
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

        <input type="hidden" name="filter_order" value="<?php echo $this -> sortColumn; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this -> sortDirection; ?>" />
       <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>

</form>