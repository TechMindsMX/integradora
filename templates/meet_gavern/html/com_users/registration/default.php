<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
jQuery(document).ready(function() {
	var $div = jQuery('#aceptarTerminos');
	var $ifr = jQuery('#aceptarTerminos iframe');
	
	var $ancho = jQuery(window).width()*.8;
	var $alto = jQuery(window).height()*.8;
	$div.height($alto);
	$ifr.width($div.width());
	$ifr.height($alto);

	jQuery('#enviar-registro').attr('disabled', 'disabled');
	
	jQuery('#aceptado').click(function() {
		var chequeado = jQuery(this).prop('checked');
		if (chequeado == true ) {
			jQuery('#enviar-registro').prop('disabled', false);
		}
		else {
			jQuery('#enviar-registro').attr('disabled', 'disabled');
		}
	});

	jQuery('#link-terminos').click(function() {
		jQuery('#aceptarTerminos iframe').contents().find("div.page-header, div#gkSocialAPI").remove();
	});
});
</script>

<div class="registration<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($fieldset->name);?>
	<?php if (count($fields)):?>
		<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.
		?>
			<legend><?php echo JText::_($fieldset->label);?></legend>
		<?php endif;?>
		<?php foreach ($fields as $field) :// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<?php echo $field->input;?>
			<?php else:?>
				<div class="control-group">
					<div class="control-label">
					<?php echo $field->label; ?>
					<?php if (!$field->required && $field->type != 'Spacer') : ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
					<?php endif; ?>
					</div>
					<div class="controls">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php endif;?>
		<?php endforeach;?>
		</fieldset>
	<?php endif;?>
<?php endforeach;?>
		<div id="terminos" class="checkbox">
			<input id="aceptado" type="checkbox" value="" name="aceptarTerminos" />
			<span><label for="aceptarTerminos"><?php echo JText::_('I_ACCEPT');?>
			<a href="#aceptarTerminos" data-toggle="modal" id="link-terminos"><?php echo JText::_('TERMS');?></a></label>
			</span>
		</div>
		<div class="form-actions">
			<button id="enviar-registro" type="submit" class="btn btn-primary validate"><?php echo JText::_('JREGISTER');?></button>
			<a class="btn" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="registration.register" />
			<?php echo JHtml::_('form.token');?>
		</div>
	</form>
</div>
	<div id="aceptarTerminos" class="modal hide fade">
		<iframe src="<?php JURI::base(); ?>index.php?option=com_content&view=article&id=25&tmpl=component"></iframe>
	</div> 
