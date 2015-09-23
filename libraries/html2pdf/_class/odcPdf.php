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
        $integrado = new IntegradoSimple($orden->integradoId);

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
                    width: 400px;
                }
                .cantidad{
                    border: 1px solid #ddd;
                }

                .cuadro{
                    border: 1px solid #ddd;
                }
                table{
                    color: #777;
                    font-size: 10px;
                    font-weight: normal;
                    line-height: 24px;
                }
                .text-right {
                    text-align: right;
                }

                .clearfix .span2 {
                    width: 125px;
                }

                .clearfix .span4 {
                    box-sizing: border-box;
                    display: block;
                    float: left;
                    margin-left: 2.5641%;
                    min-height: 28px;
                    width: 281px;
                }
                .text-center{
                    text-align: center;
                }
                h1 {
                    font-size: 28px;
                }

                </style>';
        $html .='<table style="width: 100%" id="logo">
                <tr>
                    <td style="width: 469px;">
                        <img width="150" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </td>
                    <td style="width: 120px;">
                        <h3 class=" text-right">No. Orden</h3>
                    </td>
                    <td  style="color: #999; width:73px;">
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
                        <td class="span2 text-right">
                            '.JText::_('LBL_BANCO_CUENTA').'
                        </td>
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

                <table class="table table-bordered" style=" -moz-border-bottom-colors: none;    -moz-border-left-colors: none;    -moz-border-right-colors: none;    -moz-border-top-colors: none;    border-collapse: separate;    border-color: #ddd #ddd #ddd; -moz-use-text-color;    border-image: none;    border-radius: 4px; border-style: solid solid solid none; border-width: 1px 1px 1px 0; font-size: 8px">
                    <thead>
                    <tr>
                        <th class="span1" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">#</th>
                        <th class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top; width: 20px">'.JText::_('LBL_CANTIDAD').'</th>
                        <th class="span4" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION').'</th>
                        <th class="span1" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">'.JText::_('LBL_UNIDAD').'</th>
                        <th class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top; width: 25px">'.JText::_('LBL_P_UNITARIO').'</th>
                        <th class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">'.JText::_('LBL_IMPORTE').'</th>
                    </tr>
                    </thead>
                    <tbody>';

        foreach ($orden->factura->conceptos as $key => $prod) :

            $html .='   <tr>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">';
            $html .=$key+1;
            $html .='       </td>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top; width: 20px">'.$prod['CANTIDAD'].'</td>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">'.$prod['DESCRIPCION'].'</td>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">'.$prod['UNIDAD'].'</td>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top; width: 25px">$'.number_format($prod['VALORUNITARIO'],2).'</td>
                            <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 10px;    padding: 5px;    text-align: left;    vertical-align: top;">$'.number_format($prod['IMPORTE'],2).'</td>
                        </tr>';
        endforeach;
        $html .='
                    <tr>
                        <td colspan="4" rowspan="4" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                            '.JText::_('LBL_MONTO_LETRAS').' <span>'.$number2word->toCurrency('$'.number_format($orden->totalAmount,2)).'</span>
                        </td>
                        <td class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                            '.JText::_('LBL_SUBTOTAL').'
                        </td>
                        <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">$';
        $subtotal = $orden->totalAmount - $orden->factura->impuestos->totalTrasladados;
        $html .=number_format($subtotal, 2);
        $html .='</td>
                    </tr>
                    <tr>
                        <td class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                            '.$orden->factura->impuestos->iva->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
                        </td>
                        <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                                $'.number_format($orden->factura->impuestos->iva->importe, 2).'
                        </td>
                    </tr>
                    <tr>
                        <td class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                            '.$orden->factura->impuestos->ieps->tasa.'% '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS').'
                        </td>
                        <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                                $'.number_format($orden->factura->impuestos->ieps->importe, 2).'
                        </td>
                    </tr>
                    <tr>
                        <td class="span2" style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                            '.JText::_('LBL_TOTAL').'
                        </td>
                        <td style="border-left: 1px solid #ddd; border-top: 1px solid #ddd;    line-height: 18px;    padding: 8px;    text-align: left;    vertical-align: top;">
                                $'.number_format($orden->totalAmount, 2).'
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table class="control-group" id="tabla-bottom" style="width: 450px">
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
                <br>
                <table id="footer" style="padding-left: 15px; padding-right: 15px;">
                    <tr>
                        <td>
                            '.JText::_('LBL_CON_FACTURA').'
                        </td>
                    </tr>
                    <tr>
                        <td style="text-transform: uppercase;">
                            '.JText::sprintf('LBL_AUTORIZO_ODC', $integrado->getDisplayName(), $integrado->getIntegradoRfc()).'
                        </td>
                    </tr>
                     <tr>
                        <td style="text-align: center; text-transform: uppercase;">
                            '.JText::_('LBL_INTEGRADORA').'
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            '.JText::_('LBL_INTEGRADORA_DIRECCION').'
                        </td>
                    </tr>
                </table>.
            </div>';
        return $html;
    }
}