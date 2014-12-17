<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$datos        = $this->mutuos;
?>
<div>
    <h1>Listado de Mutuos</h1>
</div>

<table class="table-bordered" style="width: 100%">
    <thead>
    <tr class="row">
        <th>Acreedor</th>
        <th>Deudor</th>
        <th>Monto</th>
        <th>Tipo de pagos</th>
        <th>Cantidad de Pagos</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($datos as $value) { ?>
        <tr class="row">
            <td><?php echo $value->integradoAcredor->nombre; ?></td>
            <td><?php echo $value->integradoDeudor->nombre; ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
        </tr>
    <?php }?>

    </tbody>
</table>
