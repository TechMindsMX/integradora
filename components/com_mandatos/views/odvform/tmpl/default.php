<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

?>

<form action="" class="form" id="altaC_P" name="altaC_P" method="post" enctype="multipart/form-data" >
<?php
echo JHtml::_('bootstrap.startTabSet', 'tabs-odv', array('active' => 'seleccion'));
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'seleccion', JText::_('COM_MANDATOS_ODV_SELECCION'));
?>
<fieldset>
    <select name="projectId">
        <option value="0">Proyecto</option>
    </select>

    <select name="projectId2">
        <option value="0">Subproyecto</option>
    </select>

    <select name="clientId">
        <option value="0">Cliente</option>
    </select>
</fieldset>

<div class="form-actions" style="max-width: 30%">
    <button type="button" class="btn btn-baja span3" id="tipoAlta"><?php echo JText::_('LBL_LIMPIAR'); ?></button>
    <button type="button" class="btn btn-primary span3" id="tipoAlta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
    <button type="button" class="btn btn-danger span3" id="tipoAlta"><?php echo JText::_('LBL_CANCELAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'ordeventa', JText::_('COM_MANDATOS_ODV_ODV'));
?>
<fieldset>
    <select name="account">
        <option value="0">Cuenta</option>
    </select>

    <select name="paymentMethod">
        <option value="0">Método de pago</option>
    </select>

    <select name="conditions">
        <option value="0">Condiciones</option>
    </select>

    <select name="placeIssue">
        <option value="0">Lugar de Expedición</option>
    </select>

    <div>
        
    </div>
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="tipoAlta"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'previewodv', JText::_('COM_MANDATOS_ODV_PREV'));
?>
<fieldset>
   pantalla 3
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>

<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'pers-juridica', JText::_('COM_MANDATOS_ODV_FACTURA'));
?>
<fieldset>
    pantalla 4
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'tabs-odv', 'pers-juridica', JText::_('COM_MANDATOS_ODV_COMPROBANTE'));
?>
<fieldset>
    pantalla 5
</fieldset>

<div class="form-actions">
    <button type="button" class="btn btn-primary span3" id="juridica"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.endTabSet');
echo JHtml::_('form.token');
?>

</form>