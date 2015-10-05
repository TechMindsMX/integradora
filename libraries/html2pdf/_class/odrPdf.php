<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 24/08/2015
 * Time: 04:45 PM
 */
jimport('integradora.integrado');
jimport('joomla.application.component.view');

class odrPdf{

    public function __construct($data){

        $this->odr = $data;
        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integCurrent = new IntegradoSimple($this->integradoId);
    }

    public function createHTML(){
        if($this->odr->paymentMethod==1){
            $metodoPago = JText::_('LBL_SPEI');
        }
        if($this->odr->paymentMethod==2) {
            $metodoPago = JText::_('LBL_DEPOSIT');
        }
        if($this->odr->paymentMethod==3) {
            $metodoPago = JText::_('LBL_CHEQUE');
        }

        $integrado = $this->integCurrent->integrados[0];
        $number2string = new AifLibNumber();
        $receptor = new IntegradoSimple($this->odr->integradoId);
        $this->odr->currency = isset($this->odr->currency)? $this->odr->currency:'MXN';

        $html ='<body>
                <table style="width: 100%" id="logo">
                <tr style="font-size: 10px">
                    <td style="width: 569px;">
                        <img width="200" src="images/logo_iecce.png" />
                    </td>
                    <td style="width: 120px;">
                        <h3 class=" text-right">No. Orden</h3>
                    </td>
                    <td >
                        <h3 class=" bordes-box text-center">'.$this->odr->numOrden.'</h3>
                    </td>
                </tr>
            </table>';
        $html .= '
                    <h1>'.JText::_('LBL_ORDEN_DE_RETIRO').'</h1>

                    <table class="clearfix" id="cabecera">
                        <tr style="font-size: 10px; line-height: 24.05px;">
                            <td class="span2 text-right" style="width: 100px;">
                                '.JText::_('LBL_SOCIO_INTEG').'
                            </td>
                            <td class="span4" style="width: 300px; line-height: 24.05px;">
                                '.$receptor->integrados[0]->datos_empresa->razon_social.'
                            </td>
                            <td class="span2 text-right" style="width: 100px;">
                                '.JText::_('LBL_DATE_CREATED').'
                            </td>
                            <td class="span4"  style="width: 200px; line-height: 24.05px;">
                                '.$this->odr->createdDate.'
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="span2 text-right" style="width: 100px; line-height: 24.05px;">
                                '.JText::_('LBL_MONEDA').'
                            </td>
                            <td class="span4" style="width: 200px; line-height: 24.05px;">
                                ';
        $this->odr->currency = isset($this->odr->currency)? $this->odr->currency:'MXN';

        $html .= $this->odr->currency.'
                            </td>
                            <td class="span2 text-right" style="width: 100px;">
                                '.JText::_('LBL_PAYMENT_DATE').'
                            </td>
                            <td class="span4" style="width: 200px;">';

        if (isset($this->odr->paymentDate)) {$html .= date('d-m-Y', $this->odr->paymentDate);}

        $html .='</td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                '.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'
                            </td>
                            <td class="span4">
                                '.$integrado->datos_personales->nombre_representante.'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_FORMA_PAGO').'
                            </td>
                            <td class="span4">
                                '.JText::_($metodoPago).'
                            </td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                '.JText::_('COM_MANDATOS_CLIENTES_PHONE').'
                            </td>
                            <td class="span4">
                                '.$integrado->datos_personales->tel_fijo.'
                            </td>
                            <td class="span2 text-right">
                            </td>
                            <td class="span4">
                            </td>
                        </tr>
                        <tr style="font-size: 10px">
                            <td class="span2 text-right">
                                '.JText::_('LBL_CORREO').'
                            </td>
                            <td class="span4">
                                '.$integrado->datos_personales->email.'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_BANCOS').'
                            </td>
                            <td class="span4">';
        if (isset($receptor->integrados[0]->datos_bancarios[0])) { $html .=$receptor->integrados[0]->datos_bancarios[0]->bankName; }
        $html .='
                            </td>
                        </tr>
                        <tr style="font-size: 10px">
                        <td style="text-align: right;">&nbsp;</td>
                        <td style="text-align: left;">&nbsp;</td>
                        <td style="text-align: right;">'.JText::_('LBL_BANCO_CUENTA').'</td>
                        <td style="text-align: left;">';
        if (isset($receptor->integrados[0]->datos_bancarios[0])) { $html .=$receptor->integrados[0]->datos_bancarios[0]->banco_cuenta; }
        $html .='</td>
                    </tr>
                    <tr style="font-size: 10px">
                        <td style="text-align: right;">&nbsp;</td>
                        <td style="text-align: left;">&nbsp;</td>
                        <td style="text-align: right;">'.JText::_('LBL_NUMERO_CLABE').'</td>
                        <td style="text-align: left;">';
                        if (isset($receptor->integrados[0]->datos_bancarios[0])) { $html .=$receptor->integrados[0]->datos_bancarios[0]->banco_clabe; }
        $html .='</td>
                    </tr>
                    </table>';
        $html .='
                        <h3>'.JText::_('LBL_DESCRIP_IMPORTE_DEPOSITAR').'</h3>
                        <table style="border: 1px solid #ddd;">
                            <thead>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px" ></th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_MONTO_LETRAS').'</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-size: 10px">
                                    <td style="border: 1px solid #ddd;">$ '.number_format($this->odr->totalAmount,2). ' ' . $this->odr->currency.'</td>
                                    <td style="border: 1px solid #ddd;">'.$number2string->toCurrency('$ '.number_format($this->odr->totalAmount,2)).'</td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="footer">
                            <tr class="container">
                                <td class="control-group" style="font-size: 10px">
                                    '.JText::_('LBL_DATOS_RETIRO').'
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px; line-height: 24px; font-size: 10px" class="container text-uppercase control-group">
                                    '.JText::sprintf('LBL_AUTORIZO_ODR',$receptor->getDisplayName(), $receptor->getIntegradoRfc()).'
                                </td>
                            </tr>
                            <tr >
                                <td style="line-height: 24px; font-size: 10px">
                                    <p class="text-capitalize" style="text-transform: capitalize; text-align: center; ">'.JText::_('LBL_INTEGRADORA').'</p>
                                    <p style="text-align: center">'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                                </td>
                            </tr>
                        </table>
                        </body>
                ';
        return $html;
    }
}