<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
jimport('joomla.html.html.bootstrap');

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
<form action="<?php echo JRoute::_('index.php?option=com_integrado&layout=edit&id=' . (int)$this -> item -> integrado -> integrado_id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_INTEGRADO_INTEGRADO_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <div class="control-label"><?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_STATUS'); ?></div>
                        <div class="controls"><select name="status">
                        	<?php foreach ($this->item->catalogos->statusSolicitud as $value): ?>
                        		<option value="<?php echo $value->status; ?>"><?php echo $value->status_name; ?></option>
                    		<?php endforeach; ?>
                        </select></div>
                    </div>
                </div>
            </div>
        </fieldset>
	<?php
	$tab_group = 'tabs-solicitud';
		echo JHtml::_('bootstrap.startTabSet', $tab_group, array('active' => 'personales'));
			tabValores($this->item->datos_personales, 'personales', $tab_group, 'LBL_SLIDE_BASIC');
			tabValores($this->item->datos_empresa, 'empresa', $tab_group, 'LBL_TAB_EMPRESA');
			tabValores($this->item->datos_bancarios, 'bancarios', $tab_group, 'LBL_TAB_BANCO');
					echo JHtml::_('bootstrap.endTabSet');
	?>
             </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="integrado.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php var_dump($this->item->catalogos); ?>