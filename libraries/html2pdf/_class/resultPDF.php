<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 17/09/2015
 * Time: 02:53 PM
 */
jimport('integradora.integrado');


class resultPDF{

    function __construct($integ_id = null, $data) {

        $integradora = new \Integralib\Integrado();
        $this->integrados = new IntegradoSimple($integradora->integradoraUuid );


        $this->name = $this->integrados->getDisplayName();
        $this->address = $this->integrados->getAddressFormatted();
        $this->rfc = $this->integrados->getIntegradoRfc();

        $this->demo = $data->demografico;

        $sesion = JFactory::getSession();
        $this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $this->reporte = $data->reporte;
        $this->ingresos = $data->ingresos;
        $this->egresos = $data->egresos;
        $this->orders = $data->reporte->findOrders( $this->integradoId );
    }

    public function generateHtml(){
        $report     = $this->reporte;
        $html ='';
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
            font-size: 13px;
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

        }

        .table-bordered {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            border-collapse: separate;
            border-color: #ddd #ddd #ddd -moz-use-text-color;
            border-image: none;
            border-radius: 4px;
            border-style: solid solid solid none;
            border-width: 1px 1px 1px 0;
        }

        .table-bordered thead:first-child tr:first-child > th:first-child, .table-bordered tbody:first-child tr:first-child > td:first-child, .table-bordered tbody:first-child tr:first-child > th:first-child {
            border-top-left-radius: 4px;
        }

        .table-bordered th, .table-bordered td {
            border-left: 1px solid #ddd;
        }

        table th, .table td {
            border-top: 1px solid #ddd;
            line-height: 18px;
            padding: 8px;
            text-align: left;
            vertical-align: top;
}
</style>
             <table class="header">
                 <tr>
                    <td colspan="3" style="width: 569px;">
                        <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </td>
                </tr>
                <tr class="span6" >
                    <td style="width: 350px">
                        <h3>
                            '.$this->name.'
                        </h3>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px; width: 350px">
                        <h3>
                            '.$this->demo->name.'
                        </h3>
                    </td>
                </tr>
                <tr class="span6">
                    <td style="width: 350px">
                            '.$this->address.'
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px;" valign="top">
                            '.$this->demo->address.'
                    </td>
                </tr>
                <tr class="span6">
                    <td style="width: 350px">
                            '.$this->rfc.'
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style=" margin-left: 30px;">
                            '.$this->demo->rfc.'
                    </td>
                </tr>
            </table>
            <br class="row-separator">

            ';

        $html .= '
        <table id="report resumen content">
            <tr>
                <td></td>
                <td></td>
                <td><h1 style="text-align: center;">'.JText::_('LBL_ESTADORESULTS').'</h1></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 240px; font-size: 14px">
                    '.JText::_('LBL_PERIOD').'
                </td>
                <td></td>
                <td style="font-size: 14px">
                    '.JText::_('LBL_RESUNE_OPERATIONS').'
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 180px">
                    <label for="startDate">'.JText::_('LBL_FROM_DATE').'</label>
                </td>
                <td style="width: 120px;">
                    '.date('d-m-Y', $this->reporte->getFechaInicio()).'
                </td>
                <td>
                    '.JText::_('LBL_INGRESOS').'
                </td>
                <td style="text-align: right;">
                    $'.number_format($this->ingresos->nominal->total,2).'
                </td>
            </tr>
            <tr>
                <td>
                    <label for="endDate">'.JText::_('LBL_TO_DATE').'</label>
                </td>
                <td>
                    '.date('d-m-Y',$this->reporte->getFechaFin()).'
                </td>
                <td>
                    '.JText::_('LBL_EGRESOS').'
                </td>
                <td style="text-align: right;">
                    $'.number_format($this->egresos->nominal->total,2).'
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
                 $'.number_format($this->ingresos->nominal->total - $this->egresos->nominal->total,2).'
                </td>
            </tr>
        </table>
        ';

    $html .='
        <h2 style="text-align: center">'.JText::_('LBL_DETAIL_OPERATIONS').'</h2>
        <h3>'.JText::_('LBL_INGRESOS').'</h3>
        <table class="table table-bordered">
            <thead>
            <tr class="row">
                <th style="width: 90px">'.JText::_('LBL_FECHA').'</th>
                <th style="width: 90px">'.JText::_('LBL_CLIENTE').'</th>
                <th style="width: 90px">'.JText::_('LBL_TYPE').'</th>
                <th style="width: 90px; text-align: right;">'.JText::_('LBL_SUBTOTAL').'</th>
                <th style="width: 90px; text-align: right;">'.JText::_('LBL_IVA').'</th>
                <th style="width: 90px; text-align: right;">'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';

            $incomeOrders = $report->getIncomeOrders();
            if ( ! empty( $incomeOrders ) ) {
                foreach ($incomeOrders as $orden) {
                    foreach ($orden->txs as $tx)
                    {
                        $html .='<tr class="row">
                            <td> '.date('d-m-Y', $tx->date).'</td>
                            <td> '.$orden->getReceptor()->getDisplayName().'</td>
                            <td> '.$orden->getOrderType() . '-'.$orden->getId() . ' - Cobrado'.'</td>
                            <td> $'.number_format($tx->detalleTx->net, 2).'</td>
                            <td> $'.number_format($tx->detalleTx->iva, 2) .'</td>
                            <td> $'.number_format($tx->detalleTx->amount,2) .'</td>
                        </tr>';
                    }
                    if ($orden->saldo->total > 0) {

                        $html .='<tr class="row">';

                            $displayDate = (isset($orden->timestamps->paymentDate) && !empty($orden->timestamps->paymentDate) ) ? $orden->timestamps->paymentDate : $orden->getCreatedDate();
                            $displayDate = (BOOL)strtotime( $displayDate ) ? $displayDate : date('d-m-Y', $displayDate);
                            $html.= '
                            <td>'.$displayDate.'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - CxC'.'</td>
                            <td>$'.number_format( ($orden->saldo->total - $orden->saldo->iva), 2).'</td>
                            <td>$'.number_format($orden->saldo->iva,2).'</td>
                            <td>$'.number_format($orden->saldo->total,2).'</td>
                        </tr>';
                    }
                }
            }
            $html .='
            <tr class="row">
                <td style="border-left: 1px solid #ddd;" colspan="5">Total de Ingresos</td>
                <td style="border-left: 1px solid #ddd;">$'.number_format($ingresos->pagado->total,2).'</td>
            </tr>
            </tbody>
        </table>

        <h3>'.JText::_('LBL_EGRESOS').'</h3>
        <table class="table table-bordered">
            <thead>
            <tr class="row">
                <th style="width: 90px">'.JText::_('LBL_FECHA').'</th>
                <th style="width: 90px">'.JText::_('LBL_PROVEEDOR').'</th>
                <th style="width: 90px">'.JText::_('LBL_TYPE').'</th>
                <th style="width: 90px">'.JText::_('LBL_SUBTOTAL').'</th>
                <th style="width: 90px">'.JText::_('LBL_IVA').'</th>
                <th style="width: 90px">'.JText::_('LBL_TOTAL').'</th>
            </tr>
            </thead>
            <tbody>';
            $expenseOrders = $report->getExpenseOrders();
            if ( ! empty( $expenseOrders ) ) {
                foreach ( $expenseOrders as $orden) {
                    foreach ($orden->txs as $tx) {

                        $html .='<tr class="row">
                            <td>'.date('d-m-Y', $tx->date).'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - Pagado'.'</td>
                            <td>$'.number_format($tx->detalleTx->net,2).'</td>
                            <td>$'.number_format($tx->detalleTx->iva,2).'</td>
                            <td>$'.number_format($tx->detalleTx->amount,2).'</td>
                        </tr>';
                    }
                    if($orden->saldo->total > 0){
                        $html .='<tr class="row">
                            <td>'.date('d-m-Y', $orden->timestamps->paymentDate).'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - CxP'.'</td>
                            <td>$'.number_format( ($orden->saldo->net), 2).'</td>
                            <td>$'.number_format($orden->saldo->iva,2).'</td>
                            <td>$'.number_format($orden->saldo->total,2).'</td>
                        </tr>';
                    }
                }
            }            $html .='<tr class="row">
                <td colspan="5">Total de Egresos</td>
                <td>$'.number_format($this->egresos->pagado->total,2).'</td>

            </tr>
            </tbody>
        </table>
    ';
        
        return $html;         
    }

}