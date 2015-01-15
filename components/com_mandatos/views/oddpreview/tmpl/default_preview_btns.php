<?php 
defined('_JEXEC') or die('Restricted access');
?>

        <div class="container botones">
        <?php
        if($this->permisos['canAuth'] && $this->odd->status->id == 1 ):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=oddpreview&layout=confirmauth&idOrden='.$this->odd->id);
        ?>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
        <?php
        endif;
        ?>
            <a class="btn btn-danger" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=oddlist'); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </div>
