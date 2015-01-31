<?php
defined('_JEXEC') or die('Restricted access');

?>
<h1><?php echo JText::_('COM_INTEGRADO_CHANGE_TITLE'); ?></h1>
<p><?php echo JText::_('COM_INTEGRADO_CHANGE_DESCRIPTION'); ?></p>

<form id="form_listados" method="post" action="index.php?option=com_integrado&task=select">
	<div class="form-group">
		<select name="integradoId" id="integradoId" class="span3">
			<option value="0">Seleccione Integrado</option>
			<?php
			foreach ($this->items as $integrado) {
				echo '<option value="'.$integrado->id.'">'.$integrado->displayName.'</option>';
			}
			?>
		</select>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-primary span3" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
?>