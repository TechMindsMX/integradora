<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$orden = $this->orden;
?>

<div class="row1 clearfix">
    <div class="span3">
        <img src="/integradora/images/logo_iecce.png" alt="Integradora - ">
    </div>
    <div class="span7" style="text-align: right; font-size: 18px; padding-top: 30px; font-weight: bolder;">
        No. Orden: <?php echo $orden->numOrden; ?>
    </div>
</div>
