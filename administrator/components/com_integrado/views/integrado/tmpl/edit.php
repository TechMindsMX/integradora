<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
jimport('joomla.html.html.bootstrap');

$integ = $this->item->integrados[0];

$nombre = (isset($integ->datos_empresa->razon_social)) ? $integ->datos_empresa->razon_social : $this->item->usuarios[0]->name;

$verifications = $this->verifications;
?>

    <form action="<?php echo JRoute::_('index.php?option=com_integrado&view=integrado&layout=edit&id=' . (int)$this -> item -> id); ?>"
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

            <fieldset class="checkboxes">
                <div>
                    <?php
                    $jhtml_group = 'slide-detalle-integrado';

                    echo JHtml::_('bootstrap.startAccordion', $jhtml_group, array('active' => 'LBL_SLIDE_BASIC'));
                    $dat_pers = isset($verifications->datos_personales)     ? $verifications->datos_personales      : '{}';
                    $dat_empr = isset($verifications->datos_empresa)        ? $verifications->datos_empresa         : '{}';
                    $dat_banc = isset($verifications->datos_bancarios[0])   ? $verifications->datos_bancarios[0]    : '{}';

                    tabValores($integ->datos_personales, 	$this->item->campos, 	$dat_pers, $jhtml_group, 'LBL_SLIDE_BASIC');
                    tabValores($integ->datos_empresa, 		$this->item->campos, 	$dat_empr, $jhtml_group, 'LBL_TAB_EMPRESA');
                    tabValores($integ->datos_bancarios[0], 	$this->item->campos, 	$dat_banc, $jhtml_group, 'LBL_TAB_BANCO');
                    echo JHtml::_('bootstrap.endAccordion');
                    ?>
                </div>
            </fieldset>
        </div>
        <input type="hidden" name="task" value="integrado.save" />
        <?php echo JHtml::_('form.token'); ?>
    </form>

<?php
function tabValores($obj, $campos, $verificacion, $jhtml_group, $jtext_label)
{
    echo JHtml::_('bootstrap.addSlide', $jhtml_group, JText::_($jtext_label), $jtext_label);
    ?>
    <div class="clearfix">
        <?php
        if ($obj) :
            ?>
            <div class="span5">
                <?php
                foreach ($obj as $label => $field):
                    if (in_array($label, $campos->$jtext_label)) :
                        if ( ! empty( $verificacion ) ) {
                            $checked = array_key_exists($label, get_object_vars(json_decode($verificacion))) ? 'checked': '';
                        }
                        ?>
                        <div class="control-group">
                            <input name="<?php echo get_class($obj).'_'.$label; ?>" type="checkbox" class="check" value="verified" <?php echo $checked; ?>>
                            <div class="control-label" style="text-transform: capitalize; padding-left: 0.5em;"><?php echo JText::_($label); ?></div>
                            <div class="controls"><?php echo $field; ?></div>
                        </div>
                    <?php
                    endif;
                endforeach;
                ?>
            </div>
            <div class="span7">
                <?php
                $attachCampos = 'attach_'.$jtext_label;
                $attachments = $campos->$attachCampos;
                foreach ($obj as $label => $field) :
                    if (in_array($label,$attachments)) :
                        ?>
                        <div class="control-group">
                            <a href="<?php echo $field; ?>"><?php echo JText::_($label); ?></a>
                        </div>
                    <?php
                    endif;
                endforeach;
                ?>
            </div>
        <?php
        endif;
        ?>
    </div>
    <?php
    echo JHtml::_('bootstrap.endSlide');
}

?>