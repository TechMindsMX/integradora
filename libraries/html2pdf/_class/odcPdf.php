<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 20/08/2015
 * Time: 01:00 PM
 */
jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');


class odcPdf
{
    public function html($orden){
        $number2word = new AifLibNumber;
        $document	 = JFactory::getDocument();
        $app 		 = JFactory::getApplication();
        $sesion      = JFactory::getSession();
        $msg         = $sesion->get('msg',null,'odcCorrecta');
        $sesion->clear('msg','odcCorrecta');
        $app->enqueueMessage($msg,'MESSAGE');


        ob_start();
        $html = '<style>
                body{
                font-size: 10px;
                }

                 .contentpane{
                            max-width: none !important;
                        }
                .table-bordered, {
                    border: 1px solid #ddd;
                    font-size: 10px;
                }
                .cantidad{
                    border: 1px solid #ddd;
                }

                .cuadro{
                    border: 1px solid #ddd;
                }

                </style>';
            $html .='<table style="width: 100%" id="logo">
                <tr>
                    <td style="width: 569px;">
                        <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </td>
                    <td style="width: 120px;">
                        <h3 class=" text-right">No. Orden</h3>
                    </td>
                    <td >
                        <h3 class=" bordes-box text-center">'.$orden->numOrden.'</h3>
                    </td>
                </tr>
            </table>';

           $html .= '<h1>'.JText::_('LBL_ORDEN_DE_COMPRA').'</h1>

            <table class="clearfix" id="cabecera">
                <tr>
                    <td class="span2 text-right">
                        '.JText::_('LBL_SOCIO_INTEG').'
                    </td>
                    <td class="span4">
                        '.$orden->emisor.'
                    </td>
                    <td class="span2 text-right">
                        '.JText::_('LBL_DATE_CREATED').'
                    </td>
                    <td class="span4">
                        '.$orden->createdDate.'
                    </td>
                </tr>
                <tr>
                    <td class="span2 text-right">
                        '.JText::_('LBL_PROY').'
                    </td>
                    <td class="span4">
                        ';
        isset($orden->proyecto->name) ? $html .= $orden->proyecto->name : $html .='';
                $html .='
                    </td>
                    <td class="span2 text-right">
                        '.JText::_('LBL_PAYMENT_DATE').'
                    </td>
                    <td class="span4">
                        '.$orden->paymentDate.'
                    </td>
                </tr>
                <tr>
                    <td class="span2 text-right">
                        '.JText::_('LBL_SUBPROY').'
                    </td>
                    <td class="span4">';

                         if (isset($orden->subproyecto->name)) { $html .=$orden->subproyecto->name; }
                $html .='</td>
                    <td class="span2 text-right">
                        '.JText::_('LBL_FORMA_PAGO').'
                    </td>
                    <td class="span4">
                        '.JText::_($orden->paymentMethod->name).'
                    </td>
                </tr>
                <tr>
                    <td class="span2 text-right">
                        '.JText::_('LBL_MONEDA').'
                    </td>
                    <td class="span4">
                        ';
            isset($orden->currency)? $html .= $orden->currency : $html .='MXN';
            $html .='
                    </td>
                </tr>
            </table>
            <div class="clearfix" id="cuerpo">
                <table class="proveedor form-group">
                    <tr>
                        <td class="span2 text-right" style="width: 100px">
                            '.JText::_('LBL_RAZON_SOCIAL').'
                        </td>
                        <td class="span10" style="150px">
                            '.$orden->proveedor->frontName.'
                        </td>
                    </tr>
                    <tr>
                        <td class="span2 text-right">
                            '.JText::_('LBL_RFC').'
                        </td>
                        <td class="span4">
                            ';
                     ($orden->proveedor->type == getFromTimOne::getPersJuridica('moral')) ? $html .=$orden->proveedor->rfc : $html .=$orden->proveedor->pRFC;
                     $html .='
                        </td>
                        <td class="span2 text-right">
                            '.JText::_('LBL_BANCOS').'
                        </td>
                        <td class="span4">';

                            if (isset($orden->dataBank)) {
                                isset($orden->dataBank[0]->bankName) ? $html .=$orden->dataBank[0]->bankName: $html .='STP';
                            }
                    $html .='
                        </td>
                    </tr>
                    <tr>
                        <td class="span2 text-right">
                            '.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'
                        </td>
                        <td class="span4">
                            '.$orden->proveedor->contact.'
                        </td>
                        <div class="span2 text-right">
                            '.JText::_('LBL_BANCO_CUENTA').'
                        </div>
                        <td class="span4">';

                            if (isset($orden->dataBank)) {
                                $banco = !isset($orden->dataBank[0]->banco_cuenta) ? 'Cuenta STP' : $orden->dataBank[0]->banco_cuenta;
                               $html .=$banco;
                            }
                        $html .= '
                        </td>
                    </tr>
                    <tr>
                        <td class="span2 text-right">
                            '.JText::_('COM_MANDATOS_CLIENTES_PHONE').'
                        </td>
                        <td class="span4">
                            '.$orden->receptor->getIntegradoPhone().'
                        </td>
                        <td class="span2 text-right">
                            '.JText::_('LBL_NUMERO_CLABE').'
                        </td>
                        <td class="span4">
                            ';
                        !empty($orden->dataBank) ? $html .=$orden->dataBank[0]->banco_clabe : $html .='';

                    $html .='
                        </td>
                    </tr>
                    <tr class="clearfix">
                        <td class="span2 text-right">
                            '.JText::_('LBL_CORREO').'
                        </td>
                        <td class="span4">
                            '.$orden->receptor->getIntegradoEmail().'
                        </td>
                    </tr>
                </table>

                <h3>'.JText::_('LBL_DESCRIP_PRODUCTOS').'</h3>

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

                    foreach ($orden->productos as $key => $prod) :

                        $html .=' <tr>
                            <td>';
                        $html .=$key+1;
                        $html .='</td>
                            <td>'.$prod['CANTIDAD'].'</td>
                            <td>'.$prod['DESCRIPCION'].'</td>
                            <td>'.$prod['UNIDAD'].'</td>
                            <td><div class="text-right">
                                    $'.number_format($prod['VALORUNITARIO'],2).'
                                </div></td>
                            <td><div class="text-right">
                                    $'.number_format($prod['IMPORTE'],2).'
                                </div></td>
                        </tr>';
                    endforeach;
                $html .='
                    <tr>
                        <td colspan="4" rowspan="4">
                            '.JText::_('LBL_MONTO_LETRAS').' <span>'.$number2word->toCurrency('$'.number_format($orden->totalAmount,2)).'</span>
                        </td>
                        <td class="span2">
                            '.JText::_('LBL_SUBTOTAL').'
                        </td>
                        <td><div class="text-right">
                                $';
                $subtotal = $orden->totalAmount - $orden->factura->impuestos->totalTrasladados;
                $html .=number_format($subtotal, 2);

                $html .='
                            </div></td>
                    </tr>
                    <tr>
                        <td class="span2">
                            '.$orden->factura->impuestos->iva->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
                        </td>
                        <td><div class="text-right">
                                $'.number_format($orden->factura->impuestos->iva->importe, 2).'
                            </div></td>
                    </tr>
                    <tr>
                        <td class="span2">
                            '.$orden->factura->impuestos->ieps->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS').'
                        </td>
                        <td><div class="text-right">
                                $'.number_format($orden->factura->impuestos->ieps->importe, 2).'
                            </div></td>
                    </tr>
                    <tr>
                        <td class="span2">
                            '.JText::_('LBL_TOTAL').'
                        </td>
                        <td><div class="text-right">
                                $'.number_format($orden->totalAmount, 2).'
                            </div></td>
                    </tr>
                    </tbody>
                </table>
                <table class="control-group" id="tabla-bottom">
                    <tr>
                        <td>
                            '.JText::_('LBL_OBSERVACIONES').'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            '.$orden->observaciones.'
                        </td>
                    </tr>
                </table>
                <table id="footer">
                    <tr class="container">
                        <td class="control-group">
                            '.JText::_('LBL_CON_FACTURA').'
                        </td>
                        <td class="container text-uppercase control-group">
                            '.JText::_('LBL_AUTORIZO_ODC').'
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>
                            <p class="text-capitalize">'.JText::_('LBL_INTEGRADORA').'</p>
                            <p>'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                        </td>
                    </tr>
                </table>
            </div>';
        return $html;
    }
}