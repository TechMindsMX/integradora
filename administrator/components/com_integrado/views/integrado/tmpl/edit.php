<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
jimport('joomla.html.html.bootstrap');

$integ = $this->item->integrados[0];
$nombre = (isset($integ->datos_empresa->razon_social)) ? $integ->datos_empresa->razon_social : $this->item->usuarios[0]->name;

function tabValores($obj, $tab_name, $tab_group, $jtext_label)
{
	echo JHtml::_('bootstrap.addTab', $tab_group, $tab_name, JText::_($jtext_label));
	?>
     <div class="span6">
        <?php 
        if ($obj) :
        foreach ($obj as $label => $field): ?>
            <div class="control-group">
                <div class="control-label"><?php echo $label; ?></div>
                <div class="controls"><?php echo $field; ?></div>
            </div>
        <?php 
        endforeach; 
        endif;
        ?>
    </div>
	<?php
		echo JHtml::_('bootstrap.endTab');

}

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
	$tab_group = 'tabs-solicitud';
		echo JHtml::_('bootstrap.startTabSet', $tab_group, array('active' => 'personales'));
			tabValores($integ->datos_personales, 'personales', $tab_group, 'LBL_SLIDE_BASIC');
			tabValores($integ->datos_empresa, 'empresa', $tab_group, 'LBL_TAB_EMPRESA');
			tabValores($integ->datos_bancarios, 'bancarios', $tab_group, 'LBL_TAB_BANCO');
					echo JHtml::_('bootstrap.endTabSet');
	?>
             </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="integrado.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>

