<?php
defined('_JEXEC') or die( 'Restricted access' );


if ( ! isset( $this->integradoId )) {
    $disale_edit = 'disabled="disabled"';
    $editUrl     = '#';
} else {
    $disale_edit = '';
    $editUrl     = JRoute::_('index.php?option=com_integrado&view=solicitud');
}

?>

<div class="container">
    <h2 class="item-title"><?php echo JText::_('LBL_ADMINSTRACION'); ?></h2>

    <div class="row-fluid">
        <div class="span4">
            <a class="button btn btn-large btn-primary span9"
               href="<?php echo JRoute::_('index.php?option=com_integrado&view=solicitud&task=createNewSolicitud'); ?>">
                <i class="icon-thumbs-up icon-3x"></i><br/><br/><?php echo JText::_('LBL_NEW_SOLICITUD'); ?>
            </a>
        </div>
        <div class="span4">
            <a class="button btn btn-large btn-primary span9" href="<?php echo $editUrl . '" ' . $disale_edit; ?>">
                <i class="icon-edit icon-3x"></i><br/><br/><?php echo JText::_('LBL_EDIT_SOLICITUD'); ?>
            </a>
        </div>
    </div>
    <br class="clearfix">
    <h4>
        <?php $name = isset( $this->items[$this->integradoId]->displayName ) ? $this->items[$this->integradoId]->displayName : '';
            echo JText::sprintf('LBL_CHANGED_TO_INTEGRADO',$name); ?></h4>
    <hr>
    <br>
    <div class="row-fluid">
        <div class="span4">
            <a class="button btn btn-large btn-primary span9"
               href="<?php echo JRoute::_('index.php?option=com_integrado&view=altausuarios'); ?>" <?php echo $disale_edit; ?>>
                <i class="icon-user icon-3x"></i><br/><br/><?php echo JText::_('LBL_ALTA_USUARIOS'); ?>
            </a>
        </div>
        <div class="span4">
            <a class="button btn btn-large btn-warning span9"
               href="<?php echo JRoute::_('index.php?option=com_integrado&view=integrado&layout=change'); ?>">
                <i class="icon-exchange icon-3x"></i><br/><br/><?php echo JText::_('COM_INTEGRADO_CHANGE_TITLE'); ?>
            </a>
        </div>
    </div>
</div>
