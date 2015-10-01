<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 02/09/2015
 * Time: 03:49 PM
 */
jimport('integradora.gettimone');

class odvPdf {

    function odv($data){

        $document	= JFactory::getDocument();
        $app 		= JFactory::getApplication();
        $template = $app->getTemplate();
        $data = $data[0];
        // Datos
        $params 	= array (
            'option' => 'com_reportes',
            'view' => 'reportescontabilidad',
            'idOrden' => $data->numOrden,
            'tmpl' => 'component',
            'print' => '1',
            'Itemid' => NULL,
        );

        $integrado = new IntegradoSimple($data->integradoId);

        $number2word = new AifLibNumber();
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
        $proyects = new getFromTimOne();
        $proyect = $proyects->getProyects($data->integradoId, $data->projectId );
        $subproyect = $proyects->getAllSubProyects($data->proyectId);
        foreach ($subproyect as $key  => $value ) {
            if($value->id_proyecto == $data->projectId2){
                $subproyecto = $value->name;
            }
        }

        $html = "<style>
            table{
                color: #777;
                font-size: 10px;
                font-weight: normal;
                line-height: 24px;
            }
        </style>
        <table class=\"table\">
            <tr>
                <td style='width: 570px;'>
                    <img width=\"200\" src=\"images/logo_iecce.png\"/>
                </td>
                <td style=\"text-align: right\">
                    <h3 class=\"text-right\">No. Orden ". $data->numOrden."</h3>
                </td>
            </tr>
        </table>";

        $html .= '
            <table class="table" id="data" style="font-size: 10px">
                <tr style="font-size: 10px" >
                    <td colspan="4"><h4>'.JText::_('LBL_ORDEN_DE_VENTA').'</h4></td>
                </tr>
                <tr >
                    <td style="text-align: right; ">'.JText::_('LBL_SOCIO_INTEG').'</td>
                    <td style="text-align: left; margin-left: 15px; width: 250px">&nbsp;&nbsp;&nbsp;'.$data->emisor->integrados[0]->datos_empresa->razon_social.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_DATE_CREATED').'</td>
                    <td style="text-align: left; width: 20%; margin-left: 15px">&nbsp;&nbsp;&nbsp;'.$data->createdDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_PROY').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;';
        isset( $proyect[$data->projectId]->name ) ? $html .= $proyect[$data->projectId]->name : $html .='';

        $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_PAYMENT_DATE').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;'.$data->paymentDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_SUBPROY').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;';
        isset($subproyecto) ? $html .= $subproyecto : $html .='';

        $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_FORMA_PAGO').'</td>
                    <td style="text-align: left;">&nbsp;&nbsp;&nbsp;'.JText::_($data->paymentMethod->name).'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_MONEDA').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;';

        isset($data->currency) ? $html .=$data->currency : $html .='MXN';

        $html .='</td>
                    <td style="text-align: right;">'.JText::_('LBL_BANCO_CUENTA').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;';
        isset($data->account[0]->banco_cuenta) ? $html .='XXXXXX' . substr($data->account[0]->banco_cuenta, -4, 4) : $html .= 'Sin Identificar';

        $receptor = $data->receptor->integrados[0]->datos_empresa;
        $html .='</td>
                </tr>
                <tr>
                    <td colspan="4"><h5>'.JText::_('LBL_HEADER_DATOS_CLIENTE').'</h5></td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RAZON_SOCIAL').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;'.$receptor->razon_social.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RFC').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;'.$receptor->rfc.'</td>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'</td>
                    <td style="text-align: left;">&nbsp;&nbsp;&nbsp;'.$receptor->razon_social.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_PHONE').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;'.$receptor->tel_fijo.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_CORREO').'</td>
                    <td style="text-align: left; margin-left: 15px">&nbsp;&nbsp;&nbsp;';
        isset($data->receptor->integrados[0]->datos_personales->email) ? $html .= $data->receptor->integrados[0]->datos_personales->email : $html .=$data->receptor->user->email;
        $html  .='</td>
                </tr>';

        $html .= '</table>';

        $html .= '<div class="clearfix"><h6>'.JText::_('LBL_DESCRIP_PRODUCTOS').'</h6></div>
                    <table style="border: 1px solid #ddd; font-size= 10px" class="table table-bordered">
                        <thead style="border: 1px solid #ddd">
                        <tr style="border: 1px solid #ddd">
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;" >#</th>
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">'.JText::_('LBL_CANTIDAD').'</th>
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd; width: 350px">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION').'</th>
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">'.JText::_('LBL_UNIDAD').'</th>
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">'.JText::_('LBL_P_UNITARIO').'</th>
                            <th style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">'.JText::_('LBL_IMPORTE').'</th>
                        </tr>
                        </thead>
                        <tbody style="border: 1px solid #ddd">';

        foreach ($data->productosData as $key => $prod){
            $html .='<tr>
                                <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">';  $html .=$key+1;
            $html .='</td><td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">';
            if ( ! empty( $prod->cantidad ) ) {
                $html .=$prod->cantidad;
            }

            $html .='</td>
                                <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">';
            if ( ! empty( $prod->descripcion ) ) {
                $html .= '<strong>'. $prod->producto .'</strong><br />'. $prod->descripcion;
            }
            $html .='</td>
                                <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">';
            if ( ! empty( $prod ) ) {
                $html .= $prod->unidad;
            }
            $html .='</td>
                                <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;"><span>$';

            if ( ! empty( $prod->p_unitario ) ) {
                $html .=number_format($prod->p_unitario,2);
            }
            $html .='</span></td>
                                <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;"><span >$';

            if ( ! empty( $prod->cantidad ) ) {
                $valor = number_format(floatval($prod->cantidad) * floatval($prod->p_unitario),2);
                $html .=$valor;
            }
            $html .='</span></td>
                            </tr>';
        }

        $html .='<tr>
                            <td colspan="4" style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                '.JText::_('LBL_MONTO_LETRAS').' <span>'.$number2word->toCurrency('$'.number_format($data->totalAmount, 2)).'</span>
                            </td>
                            <td class="span2" style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                '.JText::_('LBL_SUBTOTAL').'
                            </td>
                            <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                    $';
        $html .=number_format($data->subTotalAmount,2);

        $html .='
                             </td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td class="span2" style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
                            </td>

                            <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                    $';
        $html .= number_format($data->iva, 2);

        $html .='
                            </td>
                        </tr>
                        <tr>
                        <td colspan="4"></td>
                            <td class="span2" style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS').'
                            </td>
                            <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                    $';
        $html .=number_format($data->ieps, 2);

        $html .='
                                </td>
                        </tr>
                        <tr>
                        <td colspan="4"></td>
                            <td class="span2" style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                '.JText::_('LBL_TOTAL').'
                            </td>
                            <td style=" border-left: 1px solid #ddd; border-top: 1px solid #ddd;">
                                    $';
        $html .=number_format($data->totalAmount, 2);
        $html .='
                                </td>
                        </tr>
                        </tbody>
                    </table>
                    <br><br>
                    <table class="table" id="printFooter">
                        <tr>
                            <td>'.JText::sprintf('LBL_DATOS_DEPOSITO',$integrado->getTimoneAccount()).'</td>
                        </tr>
                        <tr>
                            <td>
                                <p style="text-transform: uppercase;">'.JText::sprintf('LBL_AUTORIZO_ODV', $integrado->getDisplayName(), $integrado->getIntegradoRfc()).'</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <p style="text-transform: uppercase;">'.JText::_('LBL_INTEGRADORA').'</p>
                                <p>'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                            </td>
                        </tr>
                    </table>';
        return $html;
    }

}