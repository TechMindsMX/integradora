<?php
defined('_JEXEC') or die('Restricted Access');
?>

<div class="content">
    <div class="well">
        <div class="row-fluid">
            <div class="col-md-6 col-sm-12 text-center span6">
                <div class="message span12">
                    <h2>
                        <?php echo JText::_('NO_QUESTIONS_ERROR_TITLE'); ?>
                    </h2>
                    <h4 class="alert alert-danger">
                        <?php echo JText::_('NO_QUESTIONS_ERROR_MSG'); ?>
                    </h4>
                    <h3>
                        <?php echo $this->integradora->getDisplayName(); ?>
                    </h3>
                    <?php
                    if ($this->integradora->getIntegradoPhone()) : ?>
                        <h4>
                            <span><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?>: </span>
                            <?php echo $this->integradora->getIntegradoPhone(); ?>
                        </h4>
                    <?php
                    endif;
                    if ($this->integradora->getIntegradoEmail()) :
                    ?>
                        <h4>
                            <span><?php echo JText::_('LBL_CORREO'); ?>: </span>
                            <a href="mailto:<?php echo $this->integradora->getIntegradoEmail(); ?>"><?php echo $this->integradora->getIntegradoEmail(); ?></a>
                        </h4>
                    <?php
                    endif;
                    ?>
                    <p>
                        <?php echo $this->integradora->getAddressFormatted(); ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 text-center span6">
                <img src="images/icons/stop_hand.png" alt="error" />
            </div>
        </div>
    </div>
</div>