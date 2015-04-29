<?php
defined('_JEXEC') or die('Restricted access');


if ( !isset( $this->integradoId ) ) {
	$disale_edit = 'disabled="disabled"';
	$editUrl = '#';
} else {
	$disale_edit = '';
	$editUrl = JRoute::_('index.php?option=com_integrado&view=solicitud');
}

?>

<h2></h2>

<a class="button btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_integrado&view=solicitud&task=createNewSolicitud'); ?>"><?php echo JText::_('LBL_NEW_SOLICITUD'); ?></a>
<a class="button btn btn-primary" href="<?php echo $editUrl. '" '.$disale_edit; ?>"><?php echo JText::_('LBL_EDIT_SOLICITUD'); ?></a>
<a class="button btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_integrado&view=altausuarios'); ?>"><?php echo JText::_('LBL_ALTA_USUARIOS'); ?></a>

<a class="button btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_integrado&view=integrado&layout=change'); ?>"><?php echo JText::_('COM_INTEGRADO_CHANGE_TITLE'); ?></a>