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
					var serializado = jQuery('form#solicitud').serialize();
					var request = jQuery.ajax({
						url: "index.php?option=com_integrado&task=saveform&format=raw",
						data: serializado+'&tab='+jQuery(this).prop('id')+'&dp_fecha_nacimiento='+jQuery('#dp_fecha_nacimiento').val(),
		 				type: 'post'
					});
				});
			});
EOD;
		
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		parent::display($tpl);
	}
}