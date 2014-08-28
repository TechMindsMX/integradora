<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document	= JFactory::getDocument();
$clientes	= $this->data;
$type		= array('Cliente', 'Proveedor');
$status		= array('Activo', 'Inactivo');

$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
?>
<script>
jQuery(document).ready(function(){
	jQuery('.btn').on('click',editarProd);
	jQuery('.filtro').on('click', filtro);
	
	jQuery('.status1 input:button').prop('disabled', true);
	
	jQuery("#myTable").tablesorter({
		sortList: [[0,0]], 
		headers: { 
			5:{ sorter: false },
			7:{ sorter: false },
			8:{ sorter: false }, 
			9:{ sorter: false }
		} 
	});
});

function filtro(){
	var valor	= parseInt( jQuery(this).val() );
	
	switch(valor){
		case 0:
			jQuery('.type_0').show();
			jQuery('.type_1').hide();
			break;
		case 1:
			jQuery('.type_1').show();
			jQuery('.type_0').hide();
			break;
		case 3:
			jQuery('.type_0').show();
			jQuery('.type_1').show();
			break;
	}
}

function editarProd(){
	var clickedBtn	= jQuery(this).prop('id');
	var productId	= clickedBtn.split('_')[1];
	
	window.location = 'index.php?option=com_mandatos&task=editarproducto&prodId='+productId;
}
</script>
<h1><?php echo JText::_('COM_MANDATOS_CLIENTES_LBL_TITULO'); ?></h1>

<div class="agregarProducto">
	<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=altaclientes'); ?>" />
		<?php echo JText::_('COM_MANDATOS_CLIENTES_LBL_AGREGAR'); ?>
	</a>

		<label for="filtro"><input type="radio" name="filtro" class="filtro" value="1"><?php echo JText::_('LBL_PROVEEDOR'); ?></label>
		<label for="filtro"><input type="radio" name="filtro" class="filtro" value="0"><?php echo JText::_('LBL_CLIENTE'); ?></label>
		<label for="filtro"><input type="radio" name="filtro" class="filtro" value="3" checked="checked">Todos</label>
		
		
		<form id="form_busqueda" method="post" action="#">
			<input type="text" name="rfc" placeholder="<?php echo JText::_('COM_MANDATOS_CLIENTES_RFC') ?>" />
			<input type="text" name="corporateName" placeholder="<?php echo JText::_('COM_MANDATOS_CLIENTES_CORPORATENAME') ?>" />
		</form>
</div>

<div class="table-responsive">
	<table id="myTable" class="table table-bordered tablesorter">
		<thead>
			<tr>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_PROVEEDOR'); ?></span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_RFC'); ?> </span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_TRADENAME'); ?> </span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_CORPORATENAME'); ?> </span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?> </span> </th>
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_STATUS'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_CLIENTES_ACCOUNT_BANK'); ?> </span> </th>
				<th style="text-align: center; vertical-align: middle;" ></th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESHABILITA_PROYECTO'); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( !is_null($clientes) ){
			foreach ($clientes as $key => $value) {
				$selected = $value->status == 0?'':'checked';
				$class = $value->status == 0?'':'status1';
				
				echo '<tr class="type_'.$value->type.'">';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$type[$value->type].'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->rfc.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->tradeName.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->corporateName.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->contact.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->phone.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$status[$value->status].'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><a>visualizar</a></td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="button" class="btn btn-primary" id="editar_'.$value->id.'" value="'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'" /></td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="checkbox" id=baja_"'.$value->id.'" name="baja" '.$selected.' /></td>';
				echo '</tr>';
			}
		}else{
			JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PRODUCTS'));
		}
		?>
		</tbody>
	</table>
</div>

	<div style="margin-top: 20px;">
		<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>" />
			<?php echo JText::_('COM_MANDATOS_TITULO'); ?>
		</a>
	</div>