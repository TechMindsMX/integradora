<?php
defined('JPATH_PLATFORM') or die;
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
jimport('integradora.integralib.order');
require('html2pdf.class.php');



class reportecontabilidad{

    public function createPDF($data, $tipo){

        if($tipo = 'odv'){
            $html = $this->odv($data);
        }

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $contenido = curl_exec($ch);
        curl_close($ch);


        $html2pdf = new HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output('respaldosPDF/sexemple.pdf', 'F');
        exit;
    }

    function odv($data){
        $document	= JFactory::getDocument();
        $app 		= JFactory::getApplication();
        $template = $app->getTemplate();

        // Datos
        $params 	= array (
                'option' => 'com_reportes',
                'view' => 'reportescontabilidad',
                'idOrden' => $data->numOrden,
                'tmpl' => 'component',
                'print' => '1',
                'Itemid' => NULL,
            );


        $number2word = new AifLibNumber();
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
        $html = "
        <table class=\"table\">
            <tr>
                <td>
                    <div><img width=\"200\" src=\"". JUri::base() . 'images/logo_iecce.png'."\"/></div>
                </td>
                <td style=\"text-align: right\">
                    <h3 class=\"text-right\">No. ". $data->numOrden."</h3>
                </td>
            </tr>
        </table>";

        $html .= '
            <table class="table" id="data">
                <tr>
                    <td colspan="4"><h4>'.JText::_('LBL_ORDEN_DE_VENTA').'</h4></td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 17%;">'.JText::_('LBL_SOCIO_INTEG').'</td>
                    <td style="text-align: left;">'.$data->emisor->integrados[0]->datos_empresa->razon_social.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_DATE_CREATED').'</td>
                    <td style="text-align: left; width: 20%;">'.$data->createdDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_PROY').'</td>

                    <td style="text-align: left;">';
                            isset( $data->proyecto->name ) ? $html .= $data->proyecto->name : $html .='';

                   $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_PAYMENT_DATE').'</td>
                    <td style="text-align: left;">'.$data->paymentDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_SUBPROY').'</td>
                    <td style="text-align: left;">';
                            isset($data->subproyecto->name) ? $html .=$data->subproyecto->name : $html .='';

                   $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_FORMA_PAGO').'</td>
                    <td style="text-align: left;">'.JText::_($data->paymentMethod->name).'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_MONEDA').'</td>
                    <td style="text-align: left;">';

                    isset($data->currency) ? $html .=$data->currency : 'MXN';

        $html .='</td>
                    <td style="text-align: right;">'.JText::_('LBL_BANCO_CUENTA').'</td>
                    <td style="text-align: left;">';
                        isset($data->account[0]->banco_cuenta) ? $html .='XXXXXX' . substr($data->account[0]->banco_cuenta, -4, 4) : '';

        $receptor = $data->receptor->integrados[0]->datos_empresa;
        $html .='</td>
                </tr>
                <tr>
                    <td colspan="5"><h5>'.JText::_('LBL_HEADER_DATOS_CLIENTE').'</h5></td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RAZON_SOCIAL').'</td>
                    <td style="text-align: left;">'.$receptor->razon_social.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RFC').'</td>
                    <td style="text-align: left;">'.$receptor->rfc.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'</td>
                    <td style="text-align: left;">'.$receptor->razon_social.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_PHONE').'</td>
                    <td style="text-align: left;">'.$receptor->tel_fijo.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_CORREO').'</td>
                    <td style="text-align: left;">';
            isset($data->receptor->integrados[0]->datos_personales->email) ? $html .= $data->receptor->integrados[0]->datos_personales->email : $html .=$data->receptor->user->email;
        $html  .='</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>';

       $html .= '</table>';

        $html .= '<div class="clearfix"><h6>'.JText::_('LBL_DESCRIP_PRODUCTOS').'</h6></div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="span1">#</th>
                            <th class="span2">'.JText::_('LBL_CANTIDAD').'</th>
                            <th class="span4">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION').'</th>
                            <th class="span1">'.JText::_('LBL_UNIDAD').'</th>
                            <th class="span2">'.JText::_('LBL_P_UNITARIO').'</th>
                            <th class="span2">'.JText::_('LBL_IMPORTE').'</th>
                        </tr>
                        </thead>
                        <tbody>';

                        foreach ($data->productosData as $key => $prod){
                            $html .='<tr>
                                <td>'; echo $key+1; $html .='</td>
                                <td>';
                                if ( ! empty( $prod->cantidad ) ) {
                                    $html .=$prod->cantidad;
                                }

                            $html .='</td>
                                <td>';
                                if ( ! empty( $prod->descripcion ) ) {
                                    $html .= '<strong>'. $prod->producto .'</strong><br />'. $prod->descripcion;
                                }
                            $html .='</td>
                                <td>';
                                if ( ! empty( $prod ) ) {
                                    $html .= $prod->unidad;
                                }
                            $html .='</td>
                                <td><div class="text-right">$';

                                if ( ! empty( $prod->p_unitario ) ) {
                                    $html .=number_format($prod->p_unitario,2);
                                }
                            $html .='</div></td>
                                <td><div class="text-right">$';

                                if ( ! empty( $prod->cantidad ) ) {
                                    $html .=number_format(floatval($prod->cantidad) * floatval($prod->p_unitario),2);
                                }
                            $html .='</div></td>
                            </tr>';
                        }

                        $html .='<tr>
                            <td colspan="4" rowspan="4">
                                '.JText::_('LBL_MONTO_LETRAS').' <span>'.$number2word->toCurrency('$'.number_format($data->totalAmount, 2)).'</span>
                            </td>
                            <td class="span2">
                                '.JText::_('LBL_SUBTOTAL').'
                            </td>
                            <td><div class="text-right">
                                    $'.number_format($data->subTotalAmount,2).'
                                </div></td>
                        </tr>
                        <tr>
                            <td class="span2">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
                            </td>
                            <td><div class="text-right">
                                    $'.number_format($data->iva, 2).'
                                </div></td>
                        </tr>
                        <tr>
                            <td class="span2">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS').'
                            </td>
                            <td><div class="text-right">
                                    $'.number_format($data->ieps, 2).'
                                </div></td>
                        </tr>
                        <tr>
                            <td class="span2">
                                '.JText::_('LBL_TOTAL').'
                            </td>
                            <td><div class="text-right">
                                    $'.number_format($data->totalAmount, 2).'
                                </div></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table" id="printFooter">
                        <tr>
                            <td>'.JText::_('LBL_CON_FACTURA').'</td>
                        </tr>
                        <tr>
                            <td>'.JText::_('LBL_AUTORIZO_FACTURA').'</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <p class="text-capitalize">'.JText::_('LBL_INTEGRADORA').'</p>
                                <p>'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                            </td>
                        </tr>
                    </table>';



        return $html;
    }
}