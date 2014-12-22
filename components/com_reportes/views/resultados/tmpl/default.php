<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
JHTML::_('behavior.calendar');

$egresos = $this->egresos;
$ingresos = $this->ingresos;

$integ = $this->integrado;
$report = $this->reporte;

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
?>
<script>
    function cambiarPeriodo() {
        fechaInicial   = jQuery('#startDate').val();
        fechaFinal     = jQuery('#endDate').val();

        window.location = 'index.php?option=com_reportes&view=resultados&integradoId=1&startDate='+fechaInicial+'&endDate='+fechaFinal;
    }

    jQuery(document).ready(function(){
        jQuery('#changePeriod').on('click',cambiarPeriodo);
    });
</script>

<div class="">
    <div class="header">
        <div class="span6">
            <h3>
                <?php echo JText::_('INTEGRADORA_NAME'); ?>
            </h3>
            <p>
                <?php echo JText::_('INTEGRADORA_ADDRESS'); ?>
            </p>
            <p>
                <?php echo JText::_('INTEGRADORA RFC'); ?>
            </p>
        </div>

        <div class="span6">
            <h3>
                <?php echo $integ->displayName; ?>
            </h3>
            <p>
                <?php echo $integ->address; ?>
            </p>
            <p>
                <?php echo $integ->datos_empresa->rfc; ?>
            </p>
        </div>
    </div>
</div>

<br class="row-separator">

<h1 class="t-center"><?php echo JText::_('LBL_ESTADORESULTS'); ?></h1>

<div id="report resumen content">
    <div class="span6">
        <h3><?php echo JText::_('LBL_PERIOD'); ?></h3>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_FROM_DATE'); ?></div>
            <div class="span6">
                <?php
                $default = date('Y-m-d',$report->period->timestamps->startDate);
                echo JHTML::_('calendar',$default,'startDate', 'startDate', $format = '%Y-%m-%d', $attsCal);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
            <div class="span6">
                <?php
                $default = date('Y-m-d',$report->period->timestamps->endDate);
                echo JHTML::_('calendar',$default,'endDate', 'endDate', $format = '%Y-%m-%d', $attsCal);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">&nbsp;</div>
            <div class="span6"><input type="button" class="btn btn-primary" id="changePeriod" value="Cambiar Periodo" /> </div>
        </div>
    </div>
    <div class="span6">
        <h3><?php echo JText::_('LBL_RESUNE_OPERATIONS'); ?></h3>
        <div class="row-fluid">

            <div class="span6">
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_INGRESOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->totalIngresos,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_EGRESOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->totalEgresos,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->totalIngresos - $report->totalEgresos,2) ;?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">&nbsp;</div>

<h2 class="t-center"><?php echo JText::_('LBL_DETAIL_OPERATIONS'); ?></h2>
<h3><?php echo JText::_('LBL_INGRESOS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_CLIENTE'); ?></th>
        <th><?php echo JText::_('LBL_ORDER_STATUS')?></th>
        <th><?php echo JText::_('LBL_CONCEPTOS'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($ingresos as $value) {
        if($value->status->id == 5){
            $status = 'CXC';
        }elseif($value->status->id == 13){
            $status = 'COBRADO';
        }
    ?>
        <tr class="row">
            <td><?php echo $value->paymentDate; ?></td>
            <td><?php echo $value->clientName; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo count($value->productosData); ?></td>
            <td>$<?php echo number_format($value->subTotalAmount,2); ?></td>
            <td>$<?php echo number_format($value->iva,2); ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
        </tr>
    <?php }?>
    <tr class="row">
        <td colspan="6">Total de Ingresos</td>
        <td>$<?php echo number_format($report->totalIngresos,2); ?></td>
    </tr>
    </tbody>
</table>

<h3><?php echo JText::_('LBL_EGRESOS'); ?></h3>
<table class="table table-bordered">
    <thead>
    <tr class="row">
        <th><?php echo JText::_('LBL_FECHA'); ?></th>
        <th><?php echo JText::_('LBL_PROVEEDOR'); ?></th>
        <th><?php echo JText::_('LBL_ORDER_STATUS')?></th>
        <th><?php echo JText::_('LBL_CONCEPTOS'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($egresos as $value) {
        if($value->status->id == 5){
            $status = 'CXP';
        }elseif($value->status->id == 13){
            $status = 'PAGADO';
        }
    ?>
        <tr class="row">
            <td><?php echo $value->paymentDate; ?></td>
            <td><?php echo $value->proveedor->tradeName; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo count($value->factura->conceptos); ?></td>
            <td>$<?php echo number_format($value->subTotalAmount,2); ?></td>
            <td>$<?php echo number_format($value->iva,2); ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
        </tr>
    <?php }?>
    <tr class="row">
        <td colspan="6">Total de Egresos</td>
        <td>$<?php echo number_format($report->totalEgresos,2); ?></td>
    </tr>
    </tbody>

</table>
</div>