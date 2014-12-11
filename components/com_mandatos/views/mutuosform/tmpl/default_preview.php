<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$contenido = JText::_('CONTENIDO_MUTUO');

$contenido = str_replace('$emisor', '<strong style="color: #000000">(CCRC) EJE 7 VIALIDAD ARTISTAS SE DE CV</strong>',$contenido);
$contenido = str_replace('$receptor', '<strong style="color: #000000">INTEGRADORA DE EMPRENDIMIENTOS CULTURALES, SA DE CV</strong>',$contenido);
$contenido = str_replace('$totalAmount', '<strong style="color: #000000">$'.number_format(20000.89,2).'</strong>',$contenido);
$contenido = str_replace('$expirationDate', '<strong style="color: #000000">28-03-2014</strong>',$contenido);
?>

<div class="row1 clearfix">
    <div class="span3">
        <img src="http://localhost/integradora/images/logo_iecce.png" alt="Integradora - ">
    </div>
    <div class="span7" style="text-align: right; font-size: 18px; padding-top: 30px; font-weight: bolder;">
        No. Orden 1
    </div>
</div>

<div class="row1 clearfix" style="padding-top: 80px;">
    <div class="span12">
    <?php echo $contenido; ?>
        </div>
</div>