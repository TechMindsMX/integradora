<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 24/08/2015
 * Time: 04:45 PM
 */

namespace html2pdf\_class;


class odrPdf{

    public function __construct($data){
        $this->odd = $data[0];
        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integCurrent = new IntegradoSimple($this->integradoId);
    }

    public function createHTML($odd)
    {


        $integrado = $this->integCurrent->integrados[0];
        $number2string = new AifLibNumber();
        $integ = new IntegradoSimple($integrado->integrado->integradoId);

        $html = '<body>
                <style>
                    body{
                        color: #777;
                        font-size: 13px;
                        font-weight: normal;
                        line-height: 24.05px;
                    }
                </style>
                <table style="width: 100%" id="logo">
                <tr style="font-size: 10px">
                    <td style="width: 569px;">
                        <img width="200" src="' . JUri::base() . 'images/logo_iecce.png' . '" />
                    </td>
                    <td style="width: 120px;">
                        <h3 class=" text-right">No. Orden</h3>
                    </td>
                    <td >
                        <h3 class=" bordes-box text-center">' . $this->odd->numOrden . '</h3>
                    </td>
                </tr>
            </table>';
        $html .= '
                    <h1>' . JText::_('LBL_ORDEN_DE_DEPOSITO') . '</h1>

                    <table class="clearfix" id="cabecera">
                        <tr style="font-size: 10px; line-height: 24.05px;">
                            <td class="span2 text-right" style="width: 100px;">
                                ' . JText::_('LBL_SOCIO_INTEG') . '
                            </td>
                            <td class="span4" style="width: 300px; line-height: 24.05px;">
                                ' . $this->odd->receptor . '
                            </td>
                            <td class="span2 text-right" style="width: 100px;">
                                ' . JText::_('LBL_DATE_CREATED') . '
                            </td>
                            <td class="span4"  style="width: 200px; line-height: 24.05px;">
                                ' . $this->odd->createdDate . '
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="span2 text-right" style="width: 100px; line-height: 24.05px;">
                                ' . JText::_('LBL_MONEDA') . '
                            </td>
                            <td class="span4" style="width: 200px; line-height: 24.05px;">
                                ';
        $this->odd->currency = isset($this->odd->currency) ? $this->odd->currency : 'MXN';

        $html .= $this->odd->currency . '
                            </td>
                            <td class="span2 text-right" style="width: 100px;">
                                ' . JText::_('LBL_PAYMENT_DATE') . '
                            </td>
                            <td class="span4" style="width: 200px;">';

        if (isset($this->odd->paymentDate)) {
            $html .= $this->odd->paymentDate;
        }

        $html .= '</td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                ' . JText::_('COM_MANDATOS_CLIENTES_CONTACT') . '
                            </td>
                            <td class="span4">
                                ' . $integ->getDisplayName() . '
                            </td>
                            <td class="span2 text-right">
                                ' . JText::_('LBL_FORMA_PAGO') . '
                            </td>
                            <td class="span4">
                                ' . JText::_($this->odd->paymentMethod->name) . '
                            </td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                ' . JText::_('COM_MANDATOS_CLIENTES_PHONE') . '
                            </td>
                            <td class="span4">
                                ' . $integrado->datos_personales->tel_fijo . '
                            </td>
                            <td class="span2 text-right">
                            </td>
                            <td class="span4">
                            </td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                ' . JText::_('LBL_CORREO') . '
                            </td>
                            <td class="span4">
                                ' . $integrado->datos_personales->email . '
                            </td>
                            <td class="span2 text-right">
                                ' . JText::_('LBL_BANCO_CUENTA') . '
                            </td>
                            <td class="span4">';
        if (isset($integrado->datos_bancarios[0]->banco_cuenta)) {
            $html .= 'XXXXXX' . substr($integrado->datos_bancarios[0]->banco_cuenta, -4, 4);
        }
        $html .= '
                            </td>
                        </tr>
                    </table>';
        $html .= '
                        <h3>' . JText::_('LBL_DESCRIP_IMPORTE_DEPOSITAR') . '</h3>
                        <table style="border: 1px solid #ddd;">
                            <thead>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px" ></th>
                                    <th style="border: 1px solid #ddd; width: 350px">' . JText::_('LBL_MONTO_LETRAS') . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-size: 10px">
                                    <td style="border: 1px solid #ddd;">$ ' . number_format($this->odd->totalAmount, 2) . ' ' . $this->odd->currency . '</td>
                                    <td style="border: 1px solid #ddd;">' . $number2string->toCurrency('$ ' . number_format($this->odd->totalAmount, 2)) . '</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="control-group" id="tabla-bottom">
                            <tr>
                                <td>
                                    ' . JText::_('LBL_OBSERVACIONES') . '
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                            </tr>
                        </table>

                        <table id="footer">
                            <tr class="container">
                                <td class="control-group" style="font-size: 10px">
                                    ' . JText::_('LBL_DATOS_DEPOSITO') . '
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px; line-height: 24px; font-size: 10px" class="container text-uppercase control-group">
                                    ' . JText::_('LBL_AUTORIZO_ODD') . '
                                </td>
                            </tr>
                            <tr >
                                <td style="line-height: 24px; font-size: 10px">
                                    <p class="text-capitalize" style="text-align: center; ">' . JText::_('LBL_INTEGRADORA') . '</p>
                                    <p style="text-align: center">' . JText::_('LBL_INTEGRADORA_DIRECCION') . '</p>
                                </td>
                            </tr>
                        </table>
                        </body>
                ';
        return $html;
    }
}