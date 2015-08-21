<?php
defined('_JEXEC') or die('Restricted Access');
?>

<div class="content">
    <div class="well">
        <div class="row row-fluid">
            <div class="col-md-6 col-sm-12 text-center span6">
                <div class="message span12">
                    <h2>
                        <?php echo JText::_('NO_QUESTIONS_ERROR_TITLE'); ?>
                    </h2>
                    <p>
                        <?php echo JText::_('NO_QUESTIONS_ERROR_MSG'); ?>
                    </p>
                    <h3>
                        <?php echo $this->integradora->getDisplayName(); ?>
                    </h3>
                    <h4>
                        <?php echo $this->integradora->getIntegradoPhone(); ?>
                    </h4>
                    <h4>
                        <?php echo $this->integradora->getIntegradoEmail(); ?>
                    </h4>
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