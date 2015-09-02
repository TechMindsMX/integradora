<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

$integ      = $this->integrado;
$report     = $this->report;
$params     = array('proyecto' => 'INT');
$input      = (object)JFactory::getApplication()->input->getArray($params);
$idProyecto = !is_null($input->proyecto) ? $input->proyecto : 0;
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"
      xmlns="http://www.w3.org/1999/html">
<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
<script src="libraries/integradora/js/tim-datepicker-defaults.js"> </script>

<script>
    function cambiarPeriodo() {
        fechaInicial   = jQuery('#startDate').val();
        fechaFinal     = jQuery('#endDate').val();

        window.location = 'index.php?option=com_reportes&view=flujo&startDate='+fechaInicial+'&endDate='+fechaFinal;
    }

    jQuery(document).ready(function(){
        jQuery('#changePeriod').on('click',cambiarPeriodo);
	    jQuery('.datepicker').datepicker();
    });
</script>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>

<div class="">
    <div class="header">
        <div class="span6">
            <h3>
                <?php echo $this->integradora->getDisplayName(); ?>
            </h3>
            <p>
                <?php echo $this->integradora->getAddressFormatted(); ?>
            </p>
            <p>
                <?php echo $this->integradora->getIntegradoRfc(); ?>
            </p>
        </div>

        <div class="span6">
            <h3>
                <?php echo $integ->getDisplayName(); ?>
            </h3>
            <p>
                <?php echo $integ->getAddressFormatted(); ?>
            </p>
            <p>
                <?php echo $integ->getIntegradoRfc(); ?>
            </p>
        </div>
    </div>
</div>

<br class="row-separator">

<h1 class="t-center"><?php echo JText::_('LBL_ESTADO_FLUJO'); ?></h1>

<div id="report resumen content">
    <div class="span6">
        <h3><?php echo JText::_('LBL_PERIOD'); ?></h3>
        <div class="row-fluid">
            <div class="span6"><label for="startDate"><?php echo JText::_('LBL_FROM_DATE'); ?></label></div>
            <div class="span6 visible-print-block"><?php echo date('d-m-Y',$report->getFechaInicio()); ?></div>
            <div class="span6 hidden-print">
	            <input class="datepicker" id="startDate" name="startDate" type="text" value="<?php echo date('Y-m-d', $report->getFechaInicio()); ?>" readonly />
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6"><label for="endDate"><?php echo JText::_('LBL_TO_DATE'); ?></label></div>
            <div class="span6 visible-print-block"><?php echo date('d-m-Y', $report->getFechaFin()); ?></div>
            <div class="span6">
	            <input class="datepicker" id="endDate" name="endDate" type="text" value="<?php echo date('Y-m-d', $report->getFechaFin()); ?>" readonly />
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">&nbsp;</div>
            <div class="span6"><input type="button" class="btn btn-primary" id="changePeriod" value="Cambiar Periodo" /> </div>
        </div>

        <div class="clearfix">&nbsp;</div>

    </div>
    <div class="span6">
        <h3><?php echo JText::_('LBL_RESUNE_OPERATIONS'); ?></h3>
        <div class="row-fluid">

            <div class="span6">
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_INGRESOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getIngresos()->amount,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_DEPOSITOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getDepositos()->amount,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getIngresos()->amount + @$report->getDepositos()->amount,2) ;?></div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_EGRESOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getEgresos()->amount ,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RETIROS'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getRetiros()->amount,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                    <div class="span6 num">$<?php echo number_format(@$report->getEgresos()->amount + @$report->getRetiros()->amount,2) ;?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">&nbsp;</div>

<h2 class="t-center"><?php echo JText::_('LBL_DETAIL_OPERATIONS'); ?></h2>

<!-- Ingresos -->
<h3><?php echo JText::_('LBL_INGRESOS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_CLIENTE'); ?></th>
        <th><?php echo JText::_('LBL_FACTURA_NUM'); ?></th>
        <th><?php echo JText::_('LBL_PROY'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ( !empty( $report->incomeTxs ) ) {
	    foreach ($report->getTxs('Integralib\OdVenta') as $k => $tx) {
	        ?>
	        <tr class="row">
	            <td><?php echo date('d-m-Y', $tx->timestamp / 1000); ?></td>
	            <td><?php echo $tx->order->getReceptor()->getDisplayName(); ?></td>
	            <td><?php echo $tx->order->getFacturaUuid(); ?></td>
	            <td><?php echo $tx->order->getProjectName().' '.$tx->order->getSubProjectName();  ?></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->net, 2); ?></div></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->iva,2) ; ?></div></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->amount,2) ; ?></div></td>
	        </tr>
	        <?php
	    }
    }
    ?>
    <tr class="row">
        <td colspan="4"><?php echo JText::_('LBL_INGRESOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getIngresos()->net,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getIngresos()->iva,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getIngresos()->amount,2); ?></div></td>
    </tr>
    </tbody>
</table>

<!-- Depósitos -->
<h3><?php echo JText::_('LBL_DEPOSITOS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_CONCEPTO'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ( !empty( $report->incomeTxs ) ) {
	    foreach ($report->getTxs('Integralib\OdDeposito') as $k => $tx) {
	        ?>
	        <tr class="row">
	            <td><?php echo date('d-m-Y', $tx->timestamp/1000); ?></td>
	            <td><?php echo $tx->order->getOrderType(); ?></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->amount, 2) ; ?></div></td>
	        </tr>
	    <?php
	    }
    }
    ?>
    <tr class="row">
        <td colspan="2"><?php echo JText::_('LBL_DEPOSITOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getDepositos()->amount,2); ?></div></td>
    </tr>
    </tbody>
</table>

<!-- Egresos -->
<h3><?php echo JText::_('LBL_EGRESOS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_PROVEEDOR'); ?></th>
        <th><?php echo JText::_('LBL_FACTURA_NUM'); ?></th>
        <th><?php echo JText::_('LBL_PROY'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ( !empty( $report->expenseTxs ) ) {
	    foreach ($report->getTxs('Integralib\OdCompra') as $k => $tx) {
	        ?>
	        <tr class="row">
	            <td><?php echo date('d-m-Y', $tx->timestamp / 1000); ?></td>
	            <td><?php echo $tx->order->getReceptor()->getDisplayName(); ?></td>
	            <td><?php echo $tx->order->getFacturaUuid(); ?></td>
	            <td><?php echo $tx->order->getProjectName().' '.$tx->order->getSubProjectName();  ?></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->net, 2); ?></div></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->iva,2) ; ?></div></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->amount,2) ; ?></div></td>
	        </tr>
	    <?php
	    }
    }
    ?>
    <tr class="row">
        <td colspan="4"><?php echo JText::_('LBL_EGRESOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getEgresos()->net,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getEgresos()->iva,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getEgresos()->amount,2); ?></div></td>
    </tr>
    </tbody>

</table>


<!-- Retiros -->
<h3><?php echo JText::_('LBL_RETIROS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_CONCEPTO'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ( !empty( $report->expenseTxs ) ) {
	    foreach ($report->getTxs('Integralib\OdRetiro') as $k => $tx) {
	        ?>
	        <tr class="row">
	            <td><?php echo date('d-m-Y', $tx->timestamp / 1000); ?></td>
	            <td><?php echo $tx->order->getReceptor()->getDisplayName(); ?></td>
	            <td><div class="text-right">$<?php echo number_format($tx->order->txs[$k]->detalleTx->amount,2) ; ?></div></td>
	        </tr>
	    <?php
	    }
    }
    ?>
    <tr class="row">
        <td colspan="2"><?php echo JText::_('LBL_RETIROS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format(@$report->getRetiros()->amount,2); ?></div></td>
    </tr>
    </tbody>
</table>

