<?php 
defined('_JEXEC') or die('Restricted access');
?>

        <div class="container botones">
        <?php
        if($this->permisos['canAuth'] && $this->odr->status === 0 ):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=odrpreview&layout=confirmauth&integradoId='.$this->integradoId.'&odrnum='.$this->odr->id);
        ?>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
        <?php
        endif;
        ?>
            <a class="btn btn-danger" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odrlist&integradoId='.$this->integradoId); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </div>
