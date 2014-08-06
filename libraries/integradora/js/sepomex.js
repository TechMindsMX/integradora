/**
 * @author Lutek TIM
 */
function datosxCP(url_ajax) {
	jQuery('input[name$="cod_postal"]').on("focusout keydown keyup click",function (e) {
		var campo 	= jQuery(this).prop('id');
		campo 		= campo.split('_');
		var code 	= e.keyCode || e.which; 
		var cp 		= jQuery('#'+campo[0]+'_cod_postal').val();
		
		if(cp.length == 5 && code != 13){
			var select_colonias = jQuery('#'+campo[0]+'colonia');
			jQuery('option', select_colonias).remove();
			
			var request = jQuery.ajax({
				url:url_ajax,
				data: {
					"cp": this.value,
					"fun": '2'
				},
				type: 'post'
			});
		
			request.done(function(result){
				var obj 			= eval('('+result+')');
				var colonias 		= obj.dAsenta;
				var select_colonias = jQuery('#'+campo[0]+'_colonia');
				var input_edos	 	= jQuery('#'+campo[0]+'_estado');
				var input_deleg 	= jQuery('#'+campo[0]+'_delegacion');
				
				jQuery('option', select_colonias).remove();
				jQuery('input', input_edos).val('');
				jQuery('input', input_deleg).val('');
									
				jQuery.each(colonias, function (key, value){
					select_colonias.append(new Option(value, value));
				});
				
				input_edos.val(obj.dEstado);
				input_deleg.val(obj.dMnpio);
			});
		
			request.fail(function (jqXHR, textStatus) {
				console.log(jqXHR);
			});
		}
	});
}