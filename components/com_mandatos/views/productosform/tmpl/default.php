<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.css');
JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.min.css');

$producto 	= $this->producto;
?>
<script>
    jQuery(document).ready(function(){
        jQuery('#cancel').on('click', cancelfunction);
    });

    function cancelfunction(){
        window.history.back();
    }
</script>

<h1><?php echo ucwords(JText::_($this->titulo)); ?></h1>

<form class="form-inline" role="form" method="post" action="index.php?option=com_mandatos&task=saveProducts">
    <input type="hidden" id="integradoId" name="integradoId" value="<?php echo $producto->integradoId; ?>">
    <input type="hidden" id="id_producto" name="id_producto" value="<?php echo $producto->id_producto; ?>">
    <input type="hidden" id="status" name="status" value="<?php echo $producto->status; ?>">

    <div class="row">
        <div class="col-md-6">
            <label for="productName"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?></label>
            <input type="text"
                   class="alto form-control"
                   id="productName"
                   name="productName"
                   value="<?php echo $producto->productName; ?>"
                   placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME') ?>">
        </div>

        <div class="col-md-6">
            <label for="currency"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?></label>
            <select name="currency"
                    id="currency">
                <option><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS'); ?></option>
                <?php
                foreach ($this->currencies as $key => $value) {
                    $selected = $value->code == $producto->currency?'selected = "selected"':'';
                    echo '<option value="'.$value->code.'"'.$selected.'>'.$value->code.'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="row">
        <div class="col-md-6">
            <label for="price"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?>: </label>
            <input type="text"
                   class="alto form-control"
                   id="price"
                   name="price"
                   value="<?php echo $producto->price; ?>"
                   placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO') ?>" />
        </div>
        <div class="col-md-6">
            <label for="iva"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>: </label>
            <input type="text"
                   class="alto form-control"
                   id="iva"
                   name="iva"
                   value="<?php echo $producto->iva ?>"
                   placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>" />
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="row">
        <div class="col-md-6">
            <label for="measure"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?>: </label>
            <input type="text" class="alto form-control" name="measure" id="measure" value="<?php echo $producto->measure; ?>">
        </div>
        <div class="col-md-6">
            <label for="ieps"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>: </label>
            <input type="text"
                   class="alto form-control"
                   id="ieps"
                   name="ieps"
                   value="<?php echo $producto->ieps; ?>"
                   placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS') ?>"/>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="row">
        <div class="col-md-6">
            <label for="description"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>: </label>
            <textarea name="description"
                      id="description"
                      rows="7"
                      style="width: 304px;"
                      placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>"><?php echo $producto->description;?></textarea>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <button type="submit" class="btn btn-primary"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    <button type="button" class="btn btn-danger" id="cancel"><?php echo JText::_('LBL_CANCELAR'); ?></button>
</form>