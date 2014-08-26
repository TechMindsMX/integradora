<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document = JFactory::getDocument();

$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
$productos = $this->data;
?>
<script>
jQuery(document).ready(function() 
    { 
        jQuery("#myTable").tablesorter(
        	{
        		sortList: [[0,0]], 
        		headers: { 
        			2:{ sorter: false },
        			3:{ sorter: false }, 
        			4:{ sorter: false }, 
        			5:{ sorter: false }, 
        			6:{ sorter: false }, 
        			7:{ sorter: false }, 
        			8:{ sorter: false }
        		} 
        	});
    } 
); 
</script>
<h1><?php echo JText::_('COM_MANDATOS_LBL_TITULO'); ?></h1>

<div class="agregarProducto">
	<?php echo JText::_('COM_MANDATOS_LBL_AGREGAR'); ?> 
</div>

<div class="table-responsive">
	<table id="myTable" class="table table-bordered tablesorter">
		<thead>
			<tr>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_NAME'); ?></span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_DESCRIPTION'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_MEDIDAS'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_PRECIO'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_IVA'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_IEPS'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LBL_MONEDA'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ></th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_LISTADO_DESHABILITA_PROYECTO'); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($productos as $key => $value) {
			$selected = $value->status == 0?'':'checked';
			$class = $value->status == 0?'':'status1';
			
			echo '<tr>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->name.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->description.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->medida.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->precio.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->iva.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->ieps.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->moneda.'</td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="button" class="btn btn-primary" id="editar_'.$value->id.'" value="'.JText::_('COM_MANDATOS_LISTADO_EDITAR_PROYECTO').'" /></td>';
			echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="radio" id=baja_"'.$value->id.'" name="baja_'.$value->id.'" '.$selected.' /></td>';
			echo '</tr>';
		} 
		?>
		</tbody>
	</table>
</div>