<?php 
defined('_JEXEC') or die('Restricted access');
?>

        <div class="container botones">
        <?php
        if($this->permisos['canAuth'] && $this->odv->status->id === 0 ):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odvpreview&layout=confirmauth&idOrden='.$this->odv->id);
        ?>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
        <?php
        endif;
        ?>
            <a class="btn btn-danger" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odvlist'); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </div>
