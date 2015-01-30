 <?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document	= JFactory::getDocument();
$clientes	= $this->data;
$type		= array('Cliente', 'Proveedor', 'Ambos');
$status		= array('Activo', 'Inactivo');
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
$document->addScript('libraries/integradora/js/form_helper.js');

if(is_null($clientes) || empty($clientes)){
	JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_CLIENTS'), 'Message');
}
?>
<script>
jQuery(document).ready(function($){
	jQuery('#search').on('click', busqueda);
	jQuery('.filtro').on('click', filtro);
	jQuery('input[name$="rfc"]').on('change', limpiarfc);
	
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

	jQuery('.baja').on('click', changeStatus);

	function changeStatus() {
		var $this = $(this);
		var itemId = $this.data('id');
		var valor = $this.is(':checked') ? 1 : 0;

		var request = $.ajax({
			url: 'index.php?option=com_mandatos&task=clienteslist.changeStatus&format=raw',
			data: {
				client_id: itemId,
				status: valor
			},
			type: 'POST',
			async: false
		});

		request.done(function (result) {
			var $row = $this.parentsUntil('table', 'tr');
			$row.removeClass('status0');
			if(typeof result.success.status != 'undefined'){
				if(result.success.status == 0) {
					$row.addClass('status0')
				}
			}

			$('#system-message-container').remove();

			var $html = '<div id="system-message-container"><div id="system-message"><div class="alert alert-message"><a data-dismiss="alert" class="close">×</a><h4 class="alert-heading">Mensaje</h4><div>';
			$html += '<p>' + result.msg + '</p></div></div></div></div>';

			$('header').prepend($html);
		});
	}

});

function limpiarfc(){
	var rfc 		= jQuery(this);
	var cadena 		= rfc.val().toUpperCase();
	var rfclimpio 	= cadena.split(' ').join('');

	rfc.val(rfclimpio);
}

function filtro(){
	var valor	= parseInt( jQuery(this).val() );
	var campoRFC = jQuery('input[name$="rfc"]');
	var campoCN  = jQuery('input[name$="corporateName"]');
	
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
			campoCN.val('');
			campoRFC.val('');
			break;
	}
}

function busqueda(){
	var campoRFC = jQuery('input[name$="rfc"]');
	var campoCN  = jQuery('input[name$="corporateName"]');
	var valorRFC = campoRFC.val();
	var valorCN  = campoCN.val();
	
	if( (valorRFC != '') && (valorCN == '') ){
		busquedapor(valorRFC, 'rfc');
	}
	
	if( (valorRFC == '') && (valorCN != '') ){
	  	busquedapor(valorCN, 'rz');
	}
	
	if( (valorRFC == '') && (valorCN == '') ){
	  	jQuery('#showall').trigger('click');
	}
	
	if( (valorRFC != '') && (valorCN != '') ){
	  	jQuery('#showall').trigger('click');
	}
}

function busquedapor(valor, campo){
	var elementos	= new Array();
	var msg			= new Array();
	
	msg['rfc'] 	= '<?php echo JText::_('COM_MANDATOS_CLIENTES_NF_RFC'); ?>';
	msg['rz'] 	= '<?php echo JText::_('COM_MANDATOS_CLIENTES_NF_CORPORATENAME'); ?>';
	
	jQuery.each(jQuery('#myTable tbody tr'), function(key, value){
		elementos[key] = jQuery(value).find('.'+campo).text();
		
		if( elementos[key].toLowerCase().indexOf( valor.toLowerCase() ) > -1 ){
			jQuery(value).show();
		}else{
			jQuery(value).hide();
		}
	});
	
	if(jQuery.inArray(valor, elementos) == -1){
		jQuery('#msg_busqueda').text(msg[campo]);
		jQuery('#msg_busqueda').fadeIn(200);
		jQuery('#msg_busqueda').fadeOut(5000);
	}
}
</script>
<h1><?php echo JText::_('COM_MANDATOS_CLIENTES_LBL_TITULO'); ?></h1>

<div>
	<div class="col-md-4">
		<a class="btn btn-primary" href="index.php?option=com_mandatos&view=clientesform"><?php echo JText::_('COM_MANDATOS_CLIENTES_LBL_AGREGAR'); ?></a>
	</div>
	
	<div class="col-md-4">
		<div class="radio">
			<label for="filtro"><input type="radio" name="filtro" class="filtro" value="1"><?php echo JText::_('LBL_PROVEEDOR'); ?></label>
			<label for="filtro"><input type="radio" name="filtro" class="filtro" value="0"><?php echo JText::_('LBL_CLIENTE'); ?></label>
			<label for="filtro"><input type="radio" name="filtro" class="filtro" value="3" id="showall" checked="checked">Todos</label>
		</div>
	</div>
	
	<div class="col-md-4">
		<input type="text" name="rfc" maxlength="13" placeholder="<?php echo JText::_('COM_MANDATOS_CLIENTES_RFC') ?>" />
		<input type="text" name="corporateName" placeholder="<?php echo JText::_('COM_MANDATOS_CLIENTES_CORPORATENAME') ?>" />
		<input type="button" class="btn btn-primary" id="search" value="buscar" />
		<span id="msg_busqueda"></span>
	</div>
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
				<th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_STATUS'); ?> </span> </th>
<!--				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta">--><?php //echo JText::_('COM_MANDATOS_CLIENTES_ACCOUNT_BANK'); ?><!-- </span> </th>-->
				<th style="text-align: center; vertical-align: middle;" ></th>
				<th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('JPUBLISHED'); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( !is_null($clientes) ){
			foreach ($clientes as $key => $value) {
				$selected = $value->status == 1 ? '' :'checked';
				$class = $value->status == 0 ? '' :'status0';
				echo '<tr class="type_'.$value->type.' '.$class.'">';
				echo '	<td style="text-align: center; vertical-align: middle;">'.$type[$value->type].'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="rfc" >'.$value->rfc.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;">'.$value->tradeName.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="rz" >'.$value->corporateName.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;">'.$value->contact.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;">'.$value->phone.'</td>';
				echo '	<td style="text-align: center; vertical-align: middle;">'.$status[$value->status];
                echo '      <div></div></td>';
//				echo '	<td style="text-align: center; vertical-align: middle;"><a>visualizar</a></td>';
				echo '	<td style="text-align: center; vertical-align: middle;">';
				echo '  	<a class="btn btn-primary" href="index.php?option=com_mandatos&view=clientesform&idCliPro='.$value->id.'">';
				echo 			JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO');
				echo '		</a>';
				echo '	</td>';
				echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="checkbox" data-id="'.$value->client_id.'" class="baja" name="baja" '.$selected.' /></td>';
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


<!-- --><?php //var_dump($clientes);
