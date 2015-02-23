<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
$mutuos = $this->mutuos;
JFactory::getDocument()->addScript('/integradora/libraries/integradora/js/jquery.tablesorter.min.js');
?>
<script>
    jQuery(document).ready(function() {
        jQuery("#tabla").tablesorter({
            sortList: [[0, 0]],
            headers: {
//                0: {sorter: false},
//                1: {sorter: false},
                2: {sorter: false},
                3: {sorter: false},
                5: {sorter: false},
                6: {sorter: false},
                7: {sorter: false}
            }
        });

        jQuery('#tabla th').removeClass('header');
    });
</script>

<div>
    <h1>Listado de Mutuos</h1>
</div>

<table id="tabla" class="table table-bordered tablesorter" style="width: 100%; text-align: center;">
    <thead>
    <tr class="row">
        <th>Id Orden</th>
        <th>Acreedor</th>
        <th>Deudor</th>
        <th>Monto</th>
        <th>Tipo de pago</th>
        <th>Cantidad de Pagos</th>
        <th>Duración</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($mutuos as $value) {
        if($value->status == 5) {
            $odpBtn = '<a href="index.php?option=com_mandatos&view=odplist&idOrden=' . $value->id . '" class="btn btn-primary">Visualizar Ordenes</a>';
        }else{
            $odpBtn = '';
        }
        ?>
        <tr class="row">
            <td><?php echo $value->id; ?></td>
            <td><?php echo $value->integradoAcredor->nombre; ?></td>
            <td><?php echo $value->integradoDeudor->nombre; ?></td>
            <td>$<?php echo number_format($value->saldo,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
            <td><?php echo $value->duracion; ?> años</td>
            <td><?php echo $odpBtn; ?></td>
        </tr>
    <?php }?>

    </tbody>
</table>
<div class="clearfix">&nbsp;</div>