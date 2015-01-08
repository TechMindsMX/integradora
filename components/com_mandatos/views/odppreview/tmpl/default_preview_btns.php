<?php
defined('_JEXEC') or die('Restricted access');
?>
<div class="container botones" style="margin-top: 60px;">
        <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odpList&integradoId=' . $this->integradoId.'&id='.$this->idMutuo); ?>"><?php echo JText::_('LBL_GO_BACK'); ?></a>
</div>