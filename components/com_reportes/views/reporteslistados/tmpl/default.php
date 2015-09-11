<?php
defined('_JEXEC') or die( 'Restricted access' );
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 05-May-15
 * Time: 1:19 PM
 */

$baseUrl       = 'index.php?option=com_reportes&view=reporteslistados&layout=';
$buttonClasses = 'btn btn-primary btn-large span9';
$iconClass = "icon-file icon-3x"
?>
<div class="container">
    <div>
        <h2><?php echo JText::_('LBL_REPORTES'); ?></h2>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'balance'; ?>">
                <i class="<?php echo $iconClass; ?>"></i>
                <br><br>

                <p><?php echo JText::_('LBL_BALANCE'); ?></p>
            </a>
        </div>
        <div class="span4">
            <a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'flujo'; ?>">
                <i class="<?php echo $iconClass; ?>"></i>
                <br><br>

                <p><?php echo JText::_('LBL_ESTADO_FLUJO'); ?></p>
            </a>
        </div>
        <div class="span4">
            <a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'resultados'; ?>">
                <i class="<?php echo $iconClass; ?>"></i>
                <br><br>

                <p><?php echo JText::_('LBL_ESTADORESULTS'); ?></p>
            </a>
        </div>
    </div>
    <div class="form-actions">
        <div class="alert alert-info">
            <p><?php echo JText::_('LBL_REPORTES_MSG'); ?></p>
        </div>
    </div>
</div>