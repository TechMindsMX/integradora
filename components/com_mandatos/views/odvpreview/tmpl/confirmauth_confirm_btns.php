 <?php 
defined('_JEXEC') or die('Restricted access');
?>

        <legend class="container botones clearfix form-actions">
        <?php
        if($this->permisos['canAuth'] && $this->odv->status === 0 ):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odvpreview&task=authorize&integradoId='.$this->integradoId.'&odvnum='.$this->odv->id);
        ?>
        	<p class="text-warning"><?php echo JText::_('LBL_CONFIRM_AUTH_ODV'); ?></p>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
        <?php
        endif;
        ?>
            <a class="btn btn-danger" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odvlist&integradoId='.$this->integradoId); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </legend>
