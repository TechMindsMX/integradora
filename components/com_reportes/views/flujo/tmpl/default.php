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
<script>
    var integradoId = <?php echo $integ->integrado->integrado_id; ?>;

    function cambiarPeriodo() {
        fechaInicial   = jQuery('#startDate').val();
        fechaFinal     = jQuery('#endDate').val();

        window.location = 'index.php?option=com_reportes&view=flujo&integradoId='+integradoId+'&startDate='+fechaInicial+'&endDate='+fechaFinal;
    }

    jQuery(document).ready(function(){
        jQuery('#changePeriod').on('click',cambiarPeriodo);
    });
</script>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>

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

<h1 class="t-center"><?php echo JText::_('LBL_ESTADO_FLUJO'); ?></h1>

<div id="report resumen content">
    <div class="span6">
        <h3><?php echo JText::_('LBL_PERIOD'); ?></h3>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_FROM_DATE'); ?></div>
            <div class="span6 visible-print-block"><?php echo $report->period->fechaInicio->format('d-m-Y'); ?></div>
            <div class="span6 hidden-print">
                <?php
            $default = $report->period->fechaInicio->format('d-m-Y');
            echo JHTML::_('calendar',$default,'startDate', 'startDate', $format = '%d-%m-%Y', $attsCal);
            ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
            <div class="span6 visible-print-block"><?php echo $report->period->fechaFin->format('d-m-Y'); ?></div>
            <div class="span6">
                <?php
                $default = $report->period->fechaFin->format('d-m-Y');
                echo JHTML::_('calendar',$default,'endDate', 'endDate', $format = '%d-%m-%Y', $attsCal);
                ?>
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
                    <div class="span6 num">$<?php echo number_format($report->ingresos->pagado->total,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_DEPOSITOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->depositos->pagado->total,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->ingresos->pagado->total + $report->depositos->pagado->total,2) ;?></div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_EGRESOS'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->egresos->pagado->total,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RETIROS'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->retiros->pagado->total,2) ;?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                    <div class="span6 num">$<?php echo number_format($report->egresos->pagado->total + $report->retiros->pagado->total,2) ;?></div>
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
        <th><?php echo JText::_('LBL_PROYECTO'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($report->orders->odv as $orden) {
        foreach ($orden->txs as $tx) {
            ?>
            <tr class="row">
                <td><?php echo date('d-m-Y', $tx->date); ?></td>
                <td><?php echo $orden->proveedor->corporateName; ?></td>
                <td><?php echo @$orden->urlXML->comprobante->FOLIO; ?></td>
                <td><?php echo @$orden->proyecto->name.' '.@$orden->sub_proyecto->name;  ?></td>
                <td><div class="text-right">$<?php echo number_format( ($tx->detalleTx->amount - $tx->detalleTx->ivaProporcion), 2); ?></div></td>
                <td><div class="text-right">$<?php echo number_format($tx->detalleTx->ivaProporcion,2) ; ?></div></td>
                <td><div class="text-right">$<?php echo number_format($tx->detalleTx->amount,2) ; ?></div></td>
            </tr>
        <?php
        }
    }
    ?>
    <tr class="row">
        <td colspan="4"><?php echo JText::_('LBL_INGRESOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format($report->ingresos->pagado->neto,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format($report->ingresos->pagado->iva,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format($report->ingresos->pagado->total,2); ?></div></td>
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
    foreach ($report->orders->odd as $orden) {
        foreach ($orden->txs as $tx) {
            ?>
            <tr class="row">
                <td><?php echo date('d-m-Y', $tx->date); ?></td>
                <td><?php echo $orden->proveedor->corporateName; ?></td>
                <td><div class="text-right">$<?php echo number_format($tx->detalleTx->amount,2) ; ?></div></td>
            </tr>
        <?php
        }
    }
    ?>
    <tr class="row">
        <td colspan="2"><?php echo JText::_('LBL_DEPOSITOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format($report->depositos->pagado->total,2); ?></div></td>
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
        <th><?php echo JText::_('LBL_PROYECTO'); ?></th>
        <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
        <th><?php echo JText::_('LBL_IVA'); ?></th>
        <th><?php echo JText::_('LBL_TOTAL'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($report->orders->odc as $orden) {
        foreach ($orden->txs as $tx) {
            ?>
            <tr class="row">
                <td><?php echo date('d-m-Y', $tx->date); ?></td>
                <td><?php echo $orden->proveedor->corporateName; ?></td>
                <td><?php echo @$orden->urlXML->comprobante->FOLIO; ?></td>
                <td><?php echo @$orden->proyecto->name.' '.@$orden->sub_proyecto->name;  ?></td>
                <td>
                    <div class="text-right">
                        $<?php echo number_format(($tx->detalleTx->amount - $tx->detalleTx->ivaProporcion), 2); ?></div>
                </td>
                <td>
                    <div class="text-right">$<?php echo number_format($tx->detalleTx->ivaProporcion, 2); ?></div>
                </td>
                <td>
                    <div class="text-right">$<?php echo number_format($tx->detalleTx->amount, 2); ?></div>
                </td>
            </tr>
        <?php
        }
    }
    ?>
    <tr class="row">
        <td colspan="4"><?php echo JText::_('LBL_EGRESOS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format($report->egresos->pagado->neto,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format($report->egresos->pagado->iva,2); ?></div></td>
        <td><div class="text-right">$<?php echo number_format($report->egresos->pagado->total,2); ?></div></td>
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
    foreach ($report->orders->odr as $orden) {
        foreach ($orden->txs as $tx) {
            ?>
            <tr class="row">
                <td><?php echo date('d-m-Y', $tx->date); ?></td>
                <td><?php echo $orden->proveedor->corporateName; ?></td>
                <td><div class="text-right">$<?php echo number_format($tx->detalleTx->amount,2) ; ?></div></td>
            </tr>
        <?php
        }
    }
    ?>
    <tr class="row">
        <td colspan="2"><?php echo JText::_('LBL_RETIROS_TOTAL'); ?></td>
        <td><div class="text-right">$<?php echo number_format($report->retiros->pagado->total,2); ?></div></td>
    </tr>
    </tbody>
</table>

