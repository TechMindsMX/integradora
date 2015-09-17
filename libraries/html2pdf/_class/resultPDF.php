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
        $this->integrado                    = new IntegradoSimple($this->integradoId);



        $this->name = $this->integrados->getDisplayName();
        $this->address = $this->integrados->getAddressFormatted();
        $this->rfc = $this->integrados->getIntegradoRfc();


        $sesion = JFactory::getSession();
        $integradora = new \Integralib\Integrado();
        $this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $reporte = new \Integralib\ReportResultados($this->integradoId, $data->inicio, $data->fin, null);
        $this->reporte = $reporte;
        $this->orders = $reporte->findOrders( $this->integradoId );


    }

    public function generateHtml(){
        $report     = $this->reporte;

        $html ='';
        $html .='
            <div class="">
                <div class="header">
                    <div class="span6">
                        <h3>
                            '.$this->name.'
                        </h3>
                        <p>
                            '.$this->address.'
                        </p>
                        <p>
                            '.$this->rfc.'
                        </p>
                    </div>
            
                    <div class="span6">
                        <h3>
                            '.$this->integrado->getDisplayName().'
                        </h3>
                        <p>
                            '.$this->integrado->getAddressFormatted().'
                        </p>
                        <p>
                            '.$this->integrado->getIntegradoRfc().'
                        </p>
                    </div>
                </div>
            </div>
            <br class="row-separator">
            <h1 class="t-center">'.JText::_('LBL_ESTADORESULTS').'</h1>
            <div id="report resumen content">
    <div class="span6">
        <h3>'.JText::_('LBL_PERIOD').'</h3>
        <div class="row-fluid">
            <div class="span6">'.JText::_('LBL_FROM_DATE').'</div>
            <div class="span6">
                ';
                $default = date('d-m-Y', $report->startPeriod);
                $html .='
                <div class="form-group">
                    <input type="text" name="startDate" id="startDate" class="datepicker" value="'.$default.'" readonly />
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">'.JText::_('LBL_TO_DATE').'</div>
            <div class="span6">
                ';
                $default = date('d-m-Y',$report->endPeriod);
                $html .='
                <div class="form-group">
                    <input type="text" name="endDate" id="endDate" class="datepicker" value="'.$default.'" readonly />
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
                    <option value="0">'.JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS').'</option>';
                    foreach($this->proyectos as $key => $value ){
                    $selected   = $key == $idProyecto?'Selected="selected"':'';
                        $html .='<option value="'.$key.'" '.$selected.'>'.$value->name.'</option>';
                    }
                $html .='</select>
            </div>
        </div>
    </div>
    <div class="span6">
                    <h3>'.JText::_('LBL_RESUNE_OPERATIONS').'</h3>
                    <div class="row-fluid">

                        <div class="span6">
                            <div class="row-fluid">
                                <div class="span6">'.JText::_('LBL_INGRESOS').'</div>
                                <div class="span6 num">$'.number_format($ingresos->nominal->total,2).'</div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6">'.JText::_('LBL_EGRESOS').'</div>
                                <div class="span6 num">$'.number_format($egresos->nominal->total,2).'</div>
                            </div>
                            <div class="row-fluid">
                                <div class="span6">'.JText::_('LBL_RESULTADO').'</div>
                                <div class="span6 num">$'.number_format($ingresos->nominal->total - $egresos->nominal->total,2).'</div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <h2 class="t-center">'.JText::_('LBL_DETAIL_OPERATIONS').'</h2>
        <h3>'.JText::_('LBL_INGRESOS').'</h3>
        <table class="table table-bordered">
            <thead>
            <tr class="row">
                <th>'.JText::_('LBL_FECHA').'</th>
                <th>'.JText::_('LBL_CLIENTE').'</th>
                <th>'.JText::_('LBL_TYPE').'</th>
                <th><div class="text-right">'.JText::_('LBL_SUBTOTAL').'</div></th>
                <th><div class="text-right">'.JText::_('LBL_IVA').'</div></th>
                <th><div class="text-right">'.JText::_('LBL_TOTAL').'</div></th>
            </tr>
            </thead>
            <tbody>';

            $incomeOrders = $report->getIncomeOrders();
            if ( ! empty( $incomeOrders ) ) {
                foreach ($incomeOrders as $orden) {
                    foreach ($orden->txs as $tx)
                    {
                        $html .='<tr class="row">
                            <td>'.date('d-m-Y', $tx->date).'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - Cobrado'.'</td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->net, 2).'</div></td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->iva, 2) .'</div></td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->amount,2) .'</div></td>
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
                            <td><div class="text-right">$'.number_format( ($orden->saldo->total - $orden->saldo->iva), 2).'</div></td>
                            <td><div class="text-right">$'.number_format($orden->saldo->iva,2).'</div></td>
                            <td><div class="text-right">$'.number_format($orden->saldo->total,2).'</div></td>
                        </tr>';
                    }
                }
            }
            $html .='
            <tr class="row">
                <td colspan="5">Total de Ingresos</td>
                <td><div class="text-right">$'.number_format($ingresos->pagado->total,2).'</div></td>
            </tr>
            </tbody>
        </table>

        <h3>'.JText::_('LBL_EGRESOS').'</h3>
        <table class="table table-bordered">
            <thead>
            <tr class="row">
                <th>'.JText::_('LBL_FECHA').'</th>
                <th>'.JText::_('LBL_PROVEEDOR').'</th>
                <th>'.JText::_('LBL_TYPE').'</th>
                <th><div class="text-right">'.JText::_('LBL_SUBTOTAL').'</div></th>
                <th><div class="text-right">'.JText::_('LBL_IVA').'</div></th>
                <th><div class="text-right">'.JText::_('LBL_TOTAL').'</div></th>
            </tr>
            </thead>
        </table>
            <!--tbody>';
            $expenseOrders = $report->getExpenseOrders();
            if ( ! empty( $expenseOrders ) ) {
                foreach ( $expenseOrders as $orden) {
                    foreach ($orden->txs as $tx) {

                        $html .='<tr class="row">
                            <td>'.date('d-m-Y', $tx->date).'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - Pagado'.'</td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->net,2).'</div></td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->iva,2).'</div></td>
                            <td><div class="text-right">$'.number_format($tx->detalleTx->amount,2).'</div></td>
                        </tr>';
                    }
                    if($orden->saldo->total > 0){
                        $html .='<tr class="row">
                            <td>'.date('d-m-Y', $orden->timestamps->paymentDate).'</td>
                            <td>'.$orden->getReceptor()->getDisplayName().'</td>
                            <td>'.$orden->getOrderType() . '-'.$orden->getId() . ' - CxP'.'</td>
                            <td><div class="text-right">$'.number_format( ($orden->saldo->net), 2).'</div></td>
                            <td><div class="text-right">$'.number_format($orden->saldo->iva,2).'</div></td>
                            <td><div class="text-right">$'.number_format($orden->saldo->total,2).'</div></td>
                        </tr>';
                    }
                }
            }
            $html .='<tr class="row">
                <td colspan="5">Total de Egresos</td>
                <td><div class="text-right">$'.number_format($egresos->pagado->total,2).'</div></td>

            </tr>
            </tbody>
        </table-->
    ';
        
        return $html;         
    }

}