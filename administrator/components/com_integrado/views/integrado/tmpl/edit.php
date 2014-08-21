<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
jimport('joomla.html.html.bootstrap');

$integ = $this->item->integrados[0];
$nombre = (isset($integ->datos_empresa->razon_social)) ? $integ->datos_empresa->razon_social : $this->item->usuarios[0]->name;

?>
<form action="<?php echo JRoute::_('index.php?option=com_integrado&layout=edit&id=' . (int)$this -> item -> id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_INTEGRADO_INTEGRADO_DETAILS').' - '.$nombre; ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <div class="control-label"><?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_STATUS'); ?></div>
                        <div class="controls">
                        	<?php foreach ($this->item->catalogos->statusSolicitud as $value): ?>
                        		<label class="radio-inline">
                        			<?php 
                        			$params = ($integ->integrado->status == $value->status) ? 'checked' : '' ;
	$expression = in_array($value->status, $this->item->transicion_status) OR $integ->integrado->status == $value->status;
                        			$params .= ($expression === true) ? '' : ' disabled' ;
                        			?>
								  <input type="radio" name="status" value="<?php echo $value->status; ?>" <?php echo $params; ?>>
								  <?php echo $value->status_name; ?>
								</label>
                    		<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
	<?php
	$jhtml_group = 'slide-detalle-integrado';

		echo JHtml::_('bootstrap.startAccordion', $jhtml_group, array('active' => 'LBL_SLIDE_BASIC'));
			tabValores($integ->datos_personales, 	'personales', 	$jhtml_group, 'LBL_SLIDE_BASIC');
			tabValores($integ->datos_empresa, 		'empresa', 		$jhtml_group, 'LBL_TAB_EMPRESA');
			tabValores($integ->datos_bancarios, 	'bancarios', 	$jhtml_group, 'LBL_TAB_BANCO');
		echo JHtml::_('bootstrap.endAccordion');
	?>
             </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="integrado.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php 
function tabValores($obj, $tab_name, $jhtml_group, $jtext_label)
{
	$campos = getCampos($jtext_label);
	
	echo JHtml::_('bootstrap.addSlide', $jhtml_group, JText::_($jtext_label), $jtext_label);
	?>
     <div>
        <?php 
        if ($obj) :
	        foreach ($obj as $label => $field): 
	        	if (in_array($label, $campos)) : 
	        	?>
	            <div class="control-group">
	                <div class="control-label"><?php echo $label; ?></div>
	                <div class="controls"><?php echo $field; ?></div>
	            </div>
	       		<?php 
	        	endif;
        	endforeach; 
    	endif;
        ?>
    </div>
	<?php
		echo JHtml::_('bootstrap.endSlide');
}

function getCampos($jtext_label) {
	$campos = array();
	switch ($jtext_label) {
		case 'LBL_SLIDE_BASIC':
			$campos = array('nacionalidad', 
							'sexo', 
							'fecha_nacimiento', 
							'RFC', 
							'calle', 
							'num_exterior', 
							'num_interior', 
							'cod_postal'
							);
			break;
		
		case 'LBL_TAB_EMPRESA':
			$campos = array('razon_social',
							'rfc'
							);
			break;
			
		case 'LBL_TAB_BANCO':
			$campos = array('nacionalidad', 
							'sexo', 
							'fecha_nacimiento', 
							'RFC', 
							'calle', 
							'num_exterior', 
							'num_interior', 
							'cod_postal'
							);
			break;
	}
	
	return $campos;
}
?>