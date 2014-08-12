<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewSolicitud extends JViewLegacy {
	
	function display($tpl = null)
	{
		$this->data = $this->get('Solicitud');
		
		$this->catalogos = $this->get('catalogos');
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$script = <<<EOD
			jQuery(document).ready(function(){
				jQuery('button').click(function(){
					var boton = jQuery(this).prop('id');
					
					if( (boton == 'juridica') || (boton == 'personales') || (boton == 'empresa') || (boton == 'bancos')){
						var serializado = jQuery('form#solicitud').serialize();
						datos = serializado
						datos += '&tab='+boton
						datos += '&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val();
						datos += '&t1_instrum_fecha='+jQuery('#t1_instrum_fecha').val();
						datos += '&t2_instrum_fecha='+jQuery('#t2_instrum_fecha').val();
						datos += '&pn_instrum_fecha='+jQuery('#pn_instrum_fecha').val();
						datos += '&rp_instrum_fecha='+jQuery('#rp_instrum_fecha').val();
						
						var request = jQuery.ajax({
							url: "index.php?option=com_integrado&task=saveform&format=raw",
							data: datos,
			 				type: 'post'
						});
						
						request.done(function(result){
							if(typeof(result) != 'object'){
								var obj = eval('('+result+')');
							}else{
								var obj = result;
							}
							
							alert(obj.msg);
						});
						
						request.fail(function (jqXHR, textStatus) {
							console.log(jqXHR, textStatus);
						});
					}
				});
			});
EOD;
		
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		parent::display($tpl);
	}
}