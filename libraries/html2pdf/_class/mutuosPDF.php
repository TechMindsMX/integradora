<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 27/08/2015
 * Time: 09:29 AM
 */

class mutuosPDF{

    public  function generateHtml($data){
        $params = $data;
        $contenido = JText::_('CONTENIDO_MUTUO');
        $date = new DateTime();

        $contenido = str_replace('$emisor', '<strong style="color: #000000">'.$params->integradoAcredor->nombre.'</strong>',$contenido);
        $contenido = str_replace('$receptor', '<strong style="color: #000000">'.$params->integradoDeudor->nombre.'</strong>',$contenido);
        $contenido = str_replace('$totalAmount', '<strong style="color: #000000">$'.number_format($params->totalAmount,2).'</strong>',$contenido);
        $contenido = str_replace('$todayDate', '<strong style="color: #000000">'.date('d-m-Y', $date->getTimestamp()).'</strong>',$contenido);
        $contenido = str_replace('&NonBreakingSpace;', ' ', $contenido);

        $tabla = json_decode($params->jsonTabla);

        $table = '';
        $totalInteres = 0;
        $totalIVA = 0;

        if($params->cuotaOcapital == 0){

            $titulo     = '<h3 style="margin-top: 60px; font-size: 20px">ANEXO 3: Tabla de amortizacion a Capital Fijo</h3>';
            foreach ($tabla->amortizacion_capital_fijo as $value) {

                if($value->periodo>28){
                    $table2 .= '<tr class="row">';
                    $table2 .= '<td>'.$value->periodo.'</td>';
                    $table2 .= '<td>$'.number_format($value->inicial, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->cuota, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->intiva, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->intereses, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->iva, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->acapital, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->final, 2).'</td>';
                    $table2 .= '</tr>';
                    $table2check = true;
                }else{
                    $table .= '<tr class="row">';
                    $table .= '<td>'.$value->periodo.'</td>';
                    $table .= '<td>$'.number_format($value->inicial, 2).'</td>';
                    $table .= '<td>$'.number_format($value->cuota, 2).'</td>';
                    $table .= '<td>$'.number_format($value->intiva, 2).'</td>';
                    $table .= '<td>$'.number_format($value->intereses, 2).'</td>';
                    $table .= '<td>$'.number_format($value->iva, 2).'</td>';
                    $table .= '<td>$'.number_format($value->acapital, 2).'</td>';
                    $table .= '<td>$'.number_format($value->final, 2).'</td>';
                    $table .= '</tr>';
                }
                $totalInteres = $totalInteres + $value->intereses;
                $totalIVA     = $totalIVA + $value->iva;
            }

            $encabezado = '<table>
                            <tr>
                                <td>Abono a Capital: $'.number_format($tabla->capital_fija,2).'</td>';
            $encabezado .= '    <td>Total de interes: $'.number_format($totalInteres,2).'</td>';
            $encabezado .= '    <td>Total de IVA: $'.number_format($totalIVA,2).'</td>
                            </tr>
                           </table>';

        }elseif($params->cuotaOcapital == 1){
            $titulo = '<h3 style="margin-top: 60px; font-size: 20px">ANEXO 3:Tabla de amortizacion a Cuota Fija</h3>';
            foreach ($tabla->amortizacion_cuota_fija as $value) {

                if($value->periodo>29){
                    $table2 .= '<tr class="row">';
                    $table2 .= '<td>'.$value->periodo.'</td>';
                    $table2 .= '<td>$'.number_format($value->inicial, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->cuota, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->intiva, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->intereses, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->iva, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->acapital, 2).'</td>';
                    $table2 .= '<td>$'.number_format($value->final, 2).'</td>';
                    $table2 .= '</tr>';
                    $table2check = true;
                }else{
                    $table .= '<tr class="row">';
                    $table .= '<td>'.$value->periodo.'</td>';
                    $table .= '<td>$'.number_format($value->inicial, 2).'</td>';
                    $table .= '<td>$'.number_format($value->cuota, 2).'</td>';
                    $table .= '<td>$'.number_format($value->intiva, 2).'</td>';
                    $table .= '<td>$'.number_format($value->intereses, 2).'</td>';
                    $table .= '<td>$'.number_format($value->iva, 2).'</td>';
                    $table .= '<td>$'.number_format($value->acapital, 2).'</td>';
                    $table .= '<td>$'.number_format($value->final, 2).'</td>';
                    $table .= '</tr>';
                }


                $totalInteres = $totalInteres + $value->intereses;
                $totalIVA     = $totalIVA + $value->iva;
            }
            $encabezado = '
                <table>
                    <tr>
                        <td>Monto de la Cuota: $'.number_format($tabla->cuota_Fija,2).'</td>';
                $encabezado .= '<td>Total de interes: $'.number_format($totalInteres,2).'</td>';
                $encabezado .= '<td>Total de IVA: $'.number_format($totalIVA,2).'</td>
                    </tr>
                </table>';
            }

        $html = '
            <style>
                .row1 .span3 {
            width: 23.0769%;
            box-sizing: border-box;
                    display: block;
                    float: left;
                }
                img {
            max-width: 100%;
                }
                body {
            color: #777;
            font-size: 13px;
                    font-weight: normal;
                    line-height: 24.05px;
                }
                h1 {
            font-size: 30px;
                }
                h1, h2, h3 {
                line-height: 20px;
                }
                h3 {
            font-size: 22.75px;
                }
                .cabeceras_mutuo, .t-center {
            text-align: center;
                }
                .table-mutuo {
                    margin-top: 50px;
                    text-align: center;
                    width: 100%;
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
                .table th, .table td {
                    border-top: 1px solid #ddd;
                    line-height: 18px;
                    padding: 8px;
                    text-align: left;
                    vertical-align: bottom;
                }

                .table-bordered th, .table-bordered td {
            border-left: 1px solid #ddd;
                }
            </style>
      <page>
        <page_header>
             <table class=".table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 390px;">
                                <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'">
                            </td>
                            <td>
                                <table>
                                    <tbody>

                                        <tr>
                                            <td style="display: inline-block; color: rgb(68, 68, 68);">
                                                <h3>
                                                    No. Orden
                                                    <span class="border-box">'.$params->id.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
             </table>
         </page_header>
            <br><br><br><br>
            <div style="color: #777; font-size: 10px; font-weight: normal; line-height: 20px;">
                <div>
                    '.$contenido.'
                </div>
            </div>
      </page>
      <page>
      <page_header>
             <table class=".table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 390px;">
                                <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'">
                            </td>
                            <td>
                                <table>
                                    <tbody>

                                        <tr>
                                            <td style="display: inline-block; color: rgb(68, 68, 68);">
                                                <h3>
                                                    No. Orden
                                                    <span class="border-box">'.$params->id.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
             </table>
         </page_header>
            <br><br>
         <div class="tabla-amortizacion" style="font-size: 10px;">
            <div class="clearfix">
                '.$titulo.'
            </div>
            '.$encabezado.'
            <table class="table table-bordered" style="width: 100%; text-align: center; font-size: 9px">
                <thead>
                <tr class="row">
                    <th>Periodo</th>
                    <th>Saldo Inicial</th>
                    <th>Couta</th>
                    <th>Interes con IVA</th>
                    <th>Interes</th>
                    <th>IVA</th>
                    <th>Abono a Capital</th>
                    <th>Saldo Final</th>
                </tr>
                </thead>
                <tbody>
                '.$table.'
                </tbody>
            </table>
         </div>
      </page>';
        if($table2check){
            $html .='<page>
                          <page_header>
                                 <table class=".table-bordered">
                                        <tbody>
                                            <tr>
                                                <td style="width: 390px;">
                                                    <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'">
                                                </td>
                                                <td>
                                                    <table>
                                                        <tbody>

                                                            <tr>
                                                                <td style="display: inline-block; color: rgb(68, 68, 68);">
                                                                    <h3>
                                                                        No. Orden
                                                                        <span class="border-box">'.$params->id.'</span>
                                                                    </h3>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                 </table>
                             </page_header>
                                <br><br>
                             <div class="tabla-amortizacion" style="font-size: 10px;">
                                <div class="clearfix">
                                    '.$titulo.'
                                </div>
                                '.$encabezado.'
                                <table class="table table-bordered" style="width: 100%; text-align: center; font-size: 9px">
                                    <thead>
                                    <tr class="row">
                                        <th>Periodo</th>
                                        <th>Saldo Inicial</th>
                                        <th>Couta</th>
                                        <th>Interes con IVA</th>
                                        <th>Interes</th>
                                        <th>IVA</th>
                                        <th>Abono a Capital</th>
                                        <th>Saldo Final</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    '.$table2.'
                                    </tbody>
                                </table>
                             </div>
                      </page>';
        }

        return $html;
    }

}