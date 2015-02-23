<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
$odps = $this->odps;
JFactory::getDocument()->addScript('/integradora/libraries/integradora/js/jquery.tablesorter.min.js');
?>
<script>
    jQuery(document).ready(function() {
        jQuery("#tabla").tablesorter({
            sortList: [[0, 0]],
            headers: {
                0: {sorter: false},
                1: {sorter: false},
                2: {sorter: false},
                3: {sorter: false},
                4: {sorter: false},
                5: {sorter: false},
                6: {sorter: false}
            }
        });

        jQuery('#tabla th').removeClass('header');
    });
</script>

<div>
    <h1>Listado de Ordenes de Prestamo</h1>
</div>

<table id="tabla" class="table table-bordered tablesorter" style="width: 100%; text-align: center;">
    <thead>
    <tr class="row">
        <th>Número Orden</th>
        <th>Fecha de Elaboración</th>
        <th>Fecha de Deposito</th>
        <th>Monto de la Orden</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($odps as $odp) {
        $style = '';
        if($odp->status == 13){
            $style = 'style="background-color: #6D9D2E;"';
        }
        ?>
        <tr class="row" <?php echo $style; ?>>
            <td><?php echo $odp->numOrden; ?></td>
            <td><?php echo date('d-m-Y', $odp->fecha_elaboracion); ?></td>
            <td><?php echo date('d-m-Y', $odp->fecha_deposito); ?></td>
            <td>$<?php echo number_format(($odp->capital + $odp->intereses + $odp->iva_intereses), 2); ?></td>
            <td><?php echo $odp->status; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="clearfix">&nbsp;</div>