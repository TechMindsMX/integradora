<?php
defined('_JEXEC') or die('Restricted access');
$data = $this->data;
?>
<script>
jQuery(document).ready(function(){
	jQuery('#email_user').on('change', functionajax);
	jQuery('#delete_user').on('click', functionajax);
});

function functionajax(campo){
	var data = '';
	var task = '';
	var campo = jQuery(this);
	
	if( campo.prop('id') == 'email_user' ){
		data = campo.val();
		task = 'checkUser';
	}else{
		data = campo.prop('name');
		task = 'deleteUser';
	}
	
	var request = jQuery.ajax({
		url: "index.php?option=com_integrado&task="+task+"&format=raw",
		data: {
			'data': data
		},
		type: 'post'
	});
		
	request.done(function(result){
		if(typeof(result) != 'object'){
			var obj = eval('('+result+')');
		}else{
			var obj = result;
		}
		
		if(obj.success){
			jQuery('#username').val(obj.name);
			jQuery('#userId').val(obj.userId);
		}else{
			alert(obj.msg);
		}
	});
	
	request.fail(function (jqXHR, textStatus) {
		console.log(jqXHR, textStatus);
	});
}
</script>
<ul>
<?php
if( count($data->usuarios) > 1 ){
	foreach ($data->usuarios as $key => $value) {
		if($value->integrado_principal == 0){
?>
			<li>
				<div>Usuario: <?php echo $value->name;?>.</div>
				<div>Nivel de Permisos: <?php echo $value->permission_level ?></div>
				<div><input type="button" class="btn btn-primary span3" name="<?php echo $value->id ?>" id="delete_user" value="<?php  echo JText::_('LBL_DELETE_USER') ?>" /></div>
			</li>
<?php
		}
	}
}
?>
</ul>
<div class="form-group">
	<label for="email_user"><?php echo JText::_('LBL_CORREO'); ?></label>
	<input type="text" name="email_user" id="email_user">
</div>


<!--a class="button" href="<?php echo JRoute::_('index.php?option=com_integrado&view=solicitud'); ?>">Ir a Solicitud de alta de Integrado</a-->

<form id="from_alta" action="index.php?option=com_integrado&task=savealta" method="post">
	<input type="hidden" name="integrado_id" value="<?php echo $data->id; ?>">
	<input type="hidden" name="userId" id="userId">
	<div class="form-group">
		<label for="email_user"><?php echo JText::_('LBL_NOMBRE'); ?></label>
		<input type="text" name="username" id="username">
	</div>
	
	<div class="form-group">
		<label><?php echo JText::_('LBL_PERMISSION_LEVEL') ?></label>
		<label for="permission_level"><input type="radio" value="0" name="permission_level" id="permission_level_0" /> 0</label>
		<label for="permission_level"><input type="radio" value="1" name="permission_level" id="permission_level_1" /> 1</label>
		<label for="permission_level"><input type="radio" value="2" name="permission_level" id="permission_level_2" /> 2</label>
		<label for="permission_level"><input type="radio" value="3" name="permission_level" id="permission_level_3" /> 3</label>
	</div>
	
	<div class="form-group">
		<button type="submit" class="btn btn-primary span3" id="files"><?php echo JText::_('LBL_ENVIAR'); ?></button>
	</div>
</form>