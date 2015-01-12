<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
JHTML::_('behavior.calendar');

$integ      = $this->integrado;
$report     = $this->reporte;
$params     = array('proyecto' => 'INT');
$input      = (object)JFactory::getApplication()->input->getArray($params);
$idProyecto = !is_null($input->proyecto)?$input->proyecto:0;
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');
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
            proyecto       = jQuery(this).val()==0?'':'&proyecto='+jQuery(this).val();

            window.location = 'index.php?option=com_reportes&view=resultados&startDate='+fechaInicial+'&endDate='+fechaFinal+proyecto;
        }

        jQuery(document).ready(function(){
            jQuery('#changePeriod').on('click',cambiarPeriodo);
            jQuery('#proyecto').on('change',filtraProyectos);
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
                $default = date('Y-m-d', $report->startPeriod);
                echo JHTML::_('calendar',$default,'startDate', 'startDate', $format = '%Y-%m-%d', $attsCal);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
            <div class="span6">
                <?php
                $default = date('Y-m-d',$report->endPeriod);
                echo JHTML::_('calendar',$default,'endDate', 'endDate', $format = '%Y-%m-%d', $attsCal);
                ?>
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
                                <div class="span6 num">$<?php echo number_format($report->ingresos->total,2) ;?></div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6"><?php echo JText::_('LBL_EGRESOS'); ?></div>
                                <div class="span6 num">$<?php echo number_format($report->egresos->total,2) ;?></div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6"><?php echo JText::_('LBL_RESULTADO'); ?></div>
                                <div class="span6 num">$<?php echo number_format($report->ingresos->total - $report->egresos->total,2) ;?></div>
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
                <th><?php echo JText::_('LBL_SUBTOTAL'); ?></th>
                <th><?php echo JText::_('LBL_IVA'); ?></th>
                <th><?php echo JText::_('LBL_TOTAL'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($report->orders->odv as $orden) {
                foreach ($orden->txs as $tx)
                {
                    ?>
                    <tr class="row">
                        <td><?php echo date('d-m-Y', $tx->date); ?></td>
                        <td><?php echo $orden->proveedor->corporateName; ?></td>
                        <td>Cobrado</td>
                        <td>$<?php echo number_format( ($tx->detalleTx->amount - $tx->detalleTx->ivaProporcion), 2); ?></td>
                        <td>$<?php echo number_format($tx->detalleTx->ivaProporcion,2) ; ?></td>
                        <td>$<?php echo number_format($tx->detalleTx->amount,2) ; ?></td>
                    </tr>
                <?php
                }
                if ($orden->saldo->total > 0) {
                    ?>
                    <tr class="row">
                        <td><?php echo date('d-m-Y', $orden->timestamps->paymentDate); ?></td>
                        <td><?php echo $orden->proveedor->corporateName; ?></td>
                        <td>CXC</td>
                        <td>$<?php echo number_format( ($orden->saldo->total - $orden->saldo->iva), 2); ?></td>
                        <td>$<?php echo number_format($orden->saldo->iva,2); ?></td>
                        <td>$<?php echo number_format($orden->saldo->total,2); ?></td>
                    </tr>
                <?php
                }
            }
            ?>
            <tr class="row">
                <td colspan="5">Total de Ingresos</td>
                <td>$<?php echo number_format($report->ingresos->total,2); ?></td>
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
                        <td>Pagado </td>
                        <td>$<?php echo number_format($tx->detalleTx->amount - $tx->detalleTx->ivaProporcion,2); ?></td>
                        <td>$<?php echo number_format($tx->detalleTx->ivaProporcion,2); ?></td>
                        <td>$<?php echo number_format($tx->detalleTx->amount,2); ?></td>
                    </tr>
                <?php
                }
                if($orden->saldo->total > 0){
                    ?>
                    <tr class="row">
                        <td><?php echo date('d-m-Y', $orden->timestamps->paymentDate); ?></td>
                        <td><?php echo $orden->proveedor->corporateName; ?></td>
                        <td>CXP</td>
                        <td>$<?php echo number_format( ($orden->saldo->total - $orden->saldo->iva), 2); ?></td>
                        <td>$<?php echo number_format($orden->saldo->iva,2); ?></td>
                        <td>$<?php echo number_format($orden->saldo->total,2); ?></td>
                    </tr>
                <?php
                }
            }
            ?>
            <tr class="row">
                <td colspan="5">Total de Egresos</td>
                <td>$<?php echo number_format($report->egresos->total,2); ?></td>

            </tr>
            </tbody>

        </table>
    </div>