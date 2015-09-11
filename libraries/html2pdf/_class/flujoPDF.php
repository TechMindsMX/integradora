<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 11/09/2015
 * Time: 10:29 AM
 */


class flujoPDF{
    function generateHtml($data){
        $integ      = $data->integ;
        $report     = $data->report;
        $params     = array('proyecto' => 'INT');
        $input      = $data->input;
        $idProyecto = $data->idProyecto;
        $attsCal    = $data->attscal;

        $html = '';
        $html .='
<style>
.header .span6 {
   box-sizing: border-box;
   display: inline-block;
   float: left;
   width: 270px;
   font-size: 15px;
}
h3 {
    font-size: 14px;
}
h2{
    font-size: 16px;
}
h1{
    font-size: 18px;
}
td {
    font-size: 10px;
}
table{
    color: #444;
    line-height: 24px;
}
</style>

            <table class="header">
                <tr class="span6">
                    <td style="width: 350px">
                        <h3>
                            '. $data->this->integradora->getDisplayName().'
                        </h3>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px;">
                        <h3>
                            '. $integ->getDisplayName().'
                        </h3>
                    </td>
                </tr>
                <tr class="span6">
                    <td>
                         '. $data->this->integradora->getAddressFormatted().'
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px;">
                        '. $integ->getAddressFormatted().'
                    </td>
                </tr>
                <tr class="span6">
                    <td>
                        '. $data->this->integradora->getIntegradoRfc().'
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px;">
                        '. $integ->getIntegradoRfc().'
                    </td>
                </tr>
            </table>
            <br>
            <h1 style=" text-align: center;">'.JText::_('LBL_ESTADO_FLUJO').'</h1>
            <br>
        ';

        $html .= '
        <table id="report resumen content">
            <tr>
                <td style="font-size: 14px">
                    '.JText::_('LBL_PERIOD').'
                </td>
                <td></td>
                <td style="font-size: 14px">
                    '.JText::_('LBL_RESUNE_OPERATIONS').'
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 180px">
                    <label for="startDate">'.JText::_('LBL_FROM_DATE').'</label>
                </td>
                <td style="width: 120px;">
                    '.date('d-m-Y',$report->getFechaInicio()).'
                </td>
                <td>
                    '.JText::_('LBL_INGRESOS').'
                </td>
                <td style="text-align: right;">
                    $'.number_format(@$report->getIngresos()->amount,2) .'
                </td>
                <td style="width: 150px">
                    '.JText::_('LBL_EGRESOS').'
                </td>
                <td style="text-align: right;">
                    $'.number_format(@$report->getEgresos()->amount ,2) .'
                </td>
            </tr>
            <tr>
                <td>
                    <label for="endDate">'.JText::_('LBL_TO_DATE').'</label>
                </td>
                <td>
                    '.date('d-m-Y', $report->getFechaFin()).'
                </td>
                <td>
                    '.JText::_('LBL_DEPOSITOS').'
                </td>
                <td style="text-align: right;">
                    $'.number_format(@$report->getDepositos()->amount,2) .'
                </td>
                <td>
                    '.JText::_('LBL_RETIROS').'
                </td>
                <td style="text-align: right;">
                   $'.number_format(@$report->getRetiros()->amount,2) .'
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                    '.JText::_('LBL_RESULTADO').'
                </td>
                <td style="text-align: right;">
                 $'.number_format(@$report->getIngresos()->amount + @$report->getDepositos()->amount,2) .'
                </td>
                <td>
                    '.JText::_('LBL_RESULTADO').'
                </td>
                <td style="text-align: right;">
                    $'.number_format(@$report->getEgresos()->amount + @$report->getRetiros()->amount,2) .'
                </td>
            </tr>
        </table>
        ';
        $html .='
        <br>
            <h2 style="text-align: center">'.JText::_('LBL_DETAIL_OPERATIONS').'</h2>
            <h3>'.JText::_('LBL_INGRESOS').'</h3>
        <br>
        ';
        
        $html .='
        <table style=" border: 1px solid #ddd; width: 550px">
            <thead>
            <tr class="row">
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_FECHA').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_CLIENTE').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_FACTURA_NUM').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_PROY').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_SUBTOTAL').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_IVA').'</th>
                <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';

            if ( !empty( $report->incomeTxs ) ) {
                foreach ($report->getTxs('Integralib\OdVenta') as $k => $tx) {

                    $html .='<tr class="row">
                        <td style=" border: 1px solid #ddd;">'.date('d-m-Y', $tx->timestamp / 1000).'</td>
                        <td style=" border: 1px solid #ddd;">$tx->order->getReceptor()->getDisplayName()</td>
                        <td style=" border: 1px solid #ddd;">'.$tx->order->getFacturaUuid().'</td>
                        <td style=" border: 1px solid #ddd;">'.$tx->order->getProjectName().' '.$tx->order->getSubProjectName().'</td>
                        <td style=" border: 1px solid #ddd;">$'.number_format($tx->order->txs[$k]->detalleTx->net, 2).'</td>
                        <td style=" border: 1px solid #ddd;">$'.number_format($tx->order->txs[$k]->detalleTx->iva,2) .'</td>
                        <td style=" border: 1px solid #ddd;">$'.number_format($tx->order->txs[$k]->detalleTx->amount,2) .'</td>
                    </tr>';

                }
            }

            $html .='<tr class="row">
                <td style=" border: 1px solid #ddd;" colspan="4">'.JText::_('LBL_INGRESOS_TOTAL').'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getIngresos()->net,2).'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getIngresos()->iva,2).'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getIngresos()->amount,2).'</td>
            </tr>
            </tbody>
        </table>
        <h3>'.JText::_('LBL_DEPOSITOS').'</h3>';
        
        $html .='
        <table style=" border: 1px solid #ddd;">
            <thead>
            <tr class="row">
                <th style=" border: 1px solid #ddd; width: 200px" >'.JText::_('LBL_FECHA').'</th>
                <th style=" border: 1px solid #ddd; width: 350px" >'.JText::_('LBL_CONCEPTO').'</th>
                <th style=" border: 1px solid #ddd; width: 200px" >'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';

            if ( !empty( $report->incomeTxs ) ) {
                foreach ($report->getTxs('Integralib\OdDeposito') as $k => $tx) {

                    $html .= '<tr class="row">
                        <td style=" border: 1px solid #ddd; width: 200px" >'.date('d-m-Y', $tx->timestamp/1000).'</td>
                        <td style=" border: 1px solid #ddd; width: 200px" >'.$tx->order->getOrderType().'</td>
                        <td style=" border: 1px solid #ddd; width: 350px" >$'.number_format($tx->order->txs[$k]->detalleTx->amount, 2) .'</td>
                    </tr>';

                }
            }
            $html .= '<tr class="row">
                <td  style=" border: 1px solid #ddd;" colspan="2">'.JText::_('LBL_DEPOSITOS_TOTAL').'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getDepositos()->amount,2).'</td>
            </tr>
            </tbody>
        </table>
        <h3>'.JText::_('LBL_EGRESOS').'</h3>';

        $html .='
            <table style=" border: 1px solid #ddd;">
                <thead>
                <tr class="row">
                    <th>'.JText::_('LBL_FECHA').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_PROVEEDOR').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_FACTURA_NUM').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_PROY').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_SUBTOTAL').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_IVA').'</th>
            <th style=" border: 1px solid #ddd; ">'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';

            if ( !empty( $report->expenseTxs ) ) {
                foreach ($report->getTxs('Integralib\OdCompra') as $k => $tx) {
                    $html .='<tr class="row">
                        <td style=" border: 1px solid #ddd;" >'.date('d-m-Y', $tx->timestamp / 1000).'</td>
                        <td style=" border: 1px solid #ddd;" >$tx->order->getReceptor()->getDisplayName()</td>
                        <td style=" border: 1px solid #ddd;" >'.$tx->order->getFacturaUuid().'</td>
                        <td style=" border: 1px solid #ddd;" >'.$tx->order->getProjectName().' '.$tx->order->getSubProjectName().'</td>
                        <td style=" border: 1px solid #ddd;" >$'.number_format($tx->order->txs[$k]->detalleTx->net, 2).'</td>
                        <td style=" border: 1px solid #ddd;" >$'.number_format($tx->order->txs[$k]->detalleTx->iva,2) .'</td>
                        <td style=" border: 1px solid #ddd;" >$'.number_format($tx->order->txs[$k]->detalleTx->amount,2) .'</td>
                    </tr>';
                }
            }

            $html .= '<tr class="row">
                <td style=" border: 1px solid #ddd;"  colspan="4">'.JText::_('LBL_EGRESOS_TOTAL').'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getEgresos()->net,2).'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getEgresos()->iva,2).'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getEgresos()->amount,2).'</td>
            </tr>
            </tbody>
            
            </table>
            <h3>'.JText::_('LBL_RETIROS').'</h3>';
        $html .='
        <table style=" border: 1px solid #ddd;">
            <thead>
            <tr class="row">
                <th style=" border: 1px solid #ddd; width: 200px">'.JText::_('LBL_FECHA').'</th>
                <th style=" border: 1px solid #ddd; width: 200px">'.JText::_('LBL_CONCEPTO').'</th>
                <th style=" border: 1px solid #ddd; width: 350px">'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';

            if ( !empty( $report->expenseTxs ) ) {
                foreach ($report->getTxs('Integralib\OdRetiro') as $k => $tx) {

                    $html .='<tr class="row">
                        <td style=" border: 1px solid #ddd;">'.date('d-m-Y', $tx->timestamp / 1000).'</td>
                        <td style=" border: 1px solid #ddd;">'.$tx->order->getReceptor()->getDisplayName().'</td>
                        <td style=" border: 1px solid #ddd;">$'.number_format($tx->order->txs[$k]->detalleTx->amount,2) .'</td>
                    </tr>';
                }
            }
            $html  .='<tr class="row">
                <td style=" border: 1px solid #ddd;" colspan="2">'.JText::_('LBL_RETIROS_TOTAL').'</td>
                <td style=" border: 1px solid #ddd;" >$'.number_format(@$report->getRetiros()->amount,2).'</td>
            </tr>
            </tbody>
        </table>
        ';
        return $html;
    }
}