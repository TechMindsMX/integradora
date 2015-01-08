<?php
defined('_JEXEC') or die('Restricted access');
if($this->orden->status == 5){
    ?>
    <div class="container botones" style="margin-top: 60px;">
        <a class="btn btn-primary"
           href="<?php echo JRoute::_('index.php?option=com_mandatos&view=mutuosList&integradoId=' . $this->integradoId); ?>"><?php echo JText::_('LBL_GO_BACK'); ?></a>
    </div>
<?php
}else {
    ?>
    <div class="container botones" style="margin-top: 60px;">
        <?php
        if ($this->permisos['canAuth']):
            $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=mutuospreview&layout=confirmauth&integradoId=' . $this->integradoId . '&idMutuo=' . $this->mutuo->id);
            ?>
            <a class="btn btn-success" href="<?php echo $authorizeURL ?>"><?php echo JText::_('LBL_AUTORIZE'); ?></a>
        <?php
        endif;
        ?>
        <a class="btn btn-danger"
           href="<?php echo JRoute::_('index.php?option=com_mandatos&view=mutuosList&integradoId=' . $this->integradoId); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
    </div>
<?php
}