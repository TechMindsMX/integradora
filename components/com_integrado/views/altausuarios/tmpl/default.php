<?php
defined('_JEXEC') or die('Restricted access');
$data = $this->data;
?>
<script>
jQuery(document).ready(function(){
	jQuery('#email_user').on('change', functionajax);
	jQuery('.delete_user').on('click', functionajax);
	jQuery('.update_user').on('click', completaform);
});

function functionajax(){
	var data 	= '';
	var task 	= '';
	var $campo 	= jQuery(this);

	if( $campo.prop('id') == 'email_user' ){
		data = $campo.val();
		task = 'checkUser';
	}else{
		data = $campo.prop('name');
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
			if(!obj.delete){
				jQuery('#username').val(obj.name);
				jQuery('#userId').val(obj.userId);
			}else{
				jQuery('#li_'+obj.id).remove();
			}
		}else{
			alert(obj.msg);
		}
	});
	
	request.fail(function (jqXHR, textStatus) {
		console.log(jqXHR, textStatus);
	});
}
function completaform(){
	var email		= jQuery(this).parent().find('.email_user_integrado').val();
	var permission	= jQuery(this).parent().find('.permission_user_integrado').val();
	var name		= jQuery(this).parent().find('.name_user_integrado').val();
	var id			= jQuery(this).parent().find('.id_user_integrado').val();
	
	jQuery('#email_user').val(email);
	jQuery('#userId').val(id);
	jQuery('#username').val(name);
	
	switch(parseInt(permission)){
		case 0:
			jQuery('#permission_level_0').prop('checked', true);
			break;
		case 1:
			jQuery('#permission_level_1').prop('checked', true);
			break;
		case 2:
			jQuery('#permission_level_2').prop('checked', true);
			break;
		case 3:
			jQuery('#permission_level_3').prop('checked', true);
			break;
	}
	
	//jQuery('#from_alta').attr('action', 'index.php?option=com_integrado&task=updatealta');
}
</script>
<ul>
<?php
if( count($data->usuarios) > 1 ){
	foreach ($data->usuarios as $key => $value) {
		if($value->integrado_principal == 0){
?>
			<li id="li_<?php echo $value->id; ?>">
				<div>Usuario: <?php echo $value->name;?>.</div>
				<div>Nivel de Permisos: <?php echo $value->permission_level ?></div>
				<div style="margin-top: 10px;">
					<input type="hidden" class="permission_user_integrado" value="<?php echo $value->permission_level; ?>" />
					<input type="hidden" class="name_user_integrado" value="<?php echo $value->name; ?>" />
					<input type="hidden" class="id_user_integrado" value="<?php echo $value->id; ?>" />
					<input type="hidden" class="email_user_integrado" value="<?php echo $value->email; ?>" />
					
					<input type="button" class="btn btn-primary span3 update_user" name="<?php echo $value->id ?>" id="update_user" value="<?php  echo JText::_('LBL_UPDATE_USER') ?>" />
					<input type="button" class="btn btn-primary span3 delete_user" name="<?php echo $value->id ?>" id="delete_user" value="<?php  echo JText::_('LBL_DELETE_USER') ?>" />
				</div>
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


<!--a class="button" href="<?php echo JRoute::_('index.php/su/template/users-manager/registration-form'); ?>">Ir a Solicitud de alta de Integrado</a-->
<form id="from_alta" action="index.php?option=com_integrado&task=saveAltaNewUserOfInteg" method="post">
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