<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

$this->document->addScript('//code.jquery.com/ui/1.11.3/jquery-ui.js');
$this->document->addScript('libraries/integradora/js/tim-datepicker-defaults.js');
$this->document->addStyleSheet('//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css');

$integ      = $this->integrado;
$report     = $this->reporte;
$params     = array('proyecto' => 'INT');
$input      = (object)JFactory::getApplication()->input->getArray($params);
$idProyecto = !is_null($input->proyecto)?$input->proyecto:0;
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

$ingresos = $report->getIngresos();
$egresos = $report->getEgresos();

?>
<script>

    function cambiarPeriodo() {
        fechaInicial   = jQuery('#startDate').val();
        fechaFinal     = jQuery('#endDate').val();

        window.location = 'index.php?option=com_reportes&view=resultados&startDate='+fechaInicial+'&endDate='+fechaFinal;
    }

    function filtraProyectos() {
        fechaInicial   = jQuery('#startDate').val();
        fechaFinal     = jQuery('#endDate').val();
        proyecto       = jQuery(this).val()==0?'':'&project='+jQuery(this).val();

        window.location = 'index.php?option=com_reportes&view=resultados&startDate='+fechaInicial+'&endDate='+fechaFinal+proyecto;
    }

    jQuery(document).ready(function(){
        jQuery('.datepicker').datepicker();
        jQuery('#changePeriod').on('click',cambiarPeriodo);
        jQuery('#proyecto').on('change',filtraProyectos);
    });
</script>

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

<h1 class="t-center"><?php echo JText::_('LBL_ESTADORESULTS'); ?></h1>

<div id="report resumen content">
    <div class="span6">
        <h3><?php echo JText::_('LBL_PERIOD'); ?></h3>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_FROM_DATE'); ?></div>
            <div class="span6">
                <?php
                $default = date('d-m-Y', $report->startPeriod);
                ?>
                <div class="form-group">
                    <input type="text" name="startDate" id="startDate" class="datepicker" value="<?php echo $default; ?>" readonly />
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
            <div class="span6">
                <?php
                $default = date('d-m-Y',$report->endPeriod);
                ?>
                <div class="form-group">
                    <input type="text" name="endDate" id="endDate" class="datepicker" value="<?php echo $default; ?>" readonly />
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">&nbsp;</div>
            <div class="span6"><input type="button" class="btn btn-primary" id="changePeriod" value="Cambiar Periodo" /> </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="row-fluid">
            <div class="span6">Filtrar por Proyecto</div>
            <div class="span6">
                <select id="proyecto" >
                    <option value="0"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS'); ?></option>
                    <?php foreach($this->proyectos as $key => $value ){
                    $selected   = $key==$idProyecto?'Selected="selected"':'';
                    ?>
                    <option value="<?php echo $key.'" '.$selected.'>'.$value->name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="span6">
                    <h3><?php echo JText::_('LBL_RESUNE_OPERATIONS'); ?></h3>
                    <div class="row-fluid">

                        <div class="span6">
                            <div class="row-fluid">
                                <div class="span6"><?php echo JText::_('LBL_INGRESOS'); ?></div>
                                <div class="span6 num">$<?php echo number_format($ingresos->nominal->total,2) ;?></div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6"><?php echo JText::_('LBL_EGRESOS'); ?></div>
                                <div class="span6 num">$<?php echo number_format($egresos->nominal->total,2) ;?></div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                                <div class="span6 num">$<?php echo number_format($ingresos->nominal->total - $egresos->nominal->total,2) ;?></div>
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
                <th><?php echo JText::_('LBL_TYPE'); ?></th>
                <th><div class="text-right"><?php echo JText::_('LBL_SUBTOTAL'); ?></div></th>
                <th><div class="text-right"><?php echo JText::_('LBL_IVA'); ?></div></th>
                <th><div class="text-right"><?php echo JText::_('LBL_TOTAL'); ?></div></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $incomeOrders = $report->getIncomeOrders();
            if ( ! empty( $incomeOrders ) ) {
                foreach ($incomeOrders as $orden) {
                    foreach ($orden->txs as $tx)
                    {
                        ?>
                        <tr class="row">
                            <td><?php echo date('d-m-Y', $tx->date); ?></td>
                            <td><?php echo $orden->getReceptor()->getDisplayName(); ?></td>
                            <td><?php echo $orden->getOrderType() . '-'.$orden->getId() . ' - Cobrado'; ?></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->net, 2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->iva, 2) ; ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->amount,2) ; ?></div></td>
                        </tr>
                        <?php
                    }
                    if ($orden->saldo->total > 0) {
                        ?>
                        <tr class="row">
                            <?php
                            $displayDate = (isset($orden->timestamps->paymentDate) && !empty($orden->timestamps->paymentDate) ) ? $orden->timestamps->paymentDate : $orden->getCreatedDate();
                            $displayDate = (BOOL)strtotime( $displayDate ) ? $displayDate : date('d-m-Y', $displayDate);
                            ?>
                            <td><?php echo $displayDate; ?></td>
                            <td><?php echo $orden->getReceptor()->getDisplayName(); ?></td>
                            <td><?php echo $orden->getOrderType() . '-'.$orden->getId() . ' - CxC'; ?></td>
                            <td><div class="text-right">$<?php echo number_format( ($orden->saldo->total - $orden->saldo->iva), 2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($orden->saldo->iva,2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($orden->saldo->total,2); ?></div></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            <tr class="row">
                <td colspan="5">Total de Ingresos</td>
                <td><div class="text-right">$<?php echo number_format($ingresos->pagado->total,2); ?></div></td>
            </tr>
            </tbody>
        </table>

        <h3><?php echo JText::_('LBL_EGRESOS'); ?></h3>
        <table class="table table-bordered">
            <thead>
            <tr class="row">
                <th><?php echo JText::_('LBL_FECHA'); ?></th>
                <th><?php echo JText::_('LBL_PROVEEDOR'); ?></th>
                <th><?php echo JText::_('LBL_TYPE'); ?></th>
                <th><div class="text-right"><?php echo JText::_('LBL_SUBTOTAL'); ?></div></th>
                <th><div class="text-right"><?php echo JText::_('LBL_IVA'); ?></div></th>
                <th><div class="text-right"><?php echo JText::_('LBL_TOTAL'); ?></div></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $expenseOrders = $report->getExpenseOrders();
            if ( ! empty( $expenseOrders ) ) {
                foreach ( $expenseOrders as $orden) {
                    foreach ($orden->txs as $tx) {
                        ?>
                        <tr class="row">
                            <td><?php echo date('d-m-Y', $tx->date); ?></td>
                            <td><?php echo $orden->getReceptor()->getDisplayName(); ?></td>
                            <td><?php echo $orden->getOrderType() . '-'.$orden->getId() . ' - Pagado'; ?></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->net,2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->iva,2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($tx->detalleTx->amount,2); ?></div></td>
                        </tr>
                        <?php
                    }
                    if($orden->saldo->total > 0){
                        ?>
                        <tr class="row">
                            <td><?php echo date('d-m-Y', $orden->timestamps->paymentDate); ?></td>
                            <td><?php echo $orden->getReceptor()->getDisplayName(); ?></td>
                            <td><?php echo $orden->getOrderType() . '-'.$orden->getId() . ' - CxP'; ?></td>
                            <td><div class="text-right">$<?php echo number_format( ($orden->saldo->net), 2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($orden->saldo->iva,2); ?></div></td>
                            <td><div class="text-right">$<?php echo number_format($orden->saldo->total,2); ?></div></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            <tr class="row">
                <td colspan="5">Total de Egresos</td>
                <td><div class="text-right">$<?php echo number_format($egresos->pagado->total,2); ?></div></td>

            </tr>
            </tbody>

        </table>
    </div>
</div>