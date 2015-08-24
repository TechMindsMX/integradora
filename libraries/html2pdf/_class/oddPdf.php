<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 21/08/2015
 * Time: 10:16 AM
 */
jimport('integradora.integrado');
class oddPdf{

    public function __construct($data){
        $this->odd = $data[0];
    }

    public function createHTML($odd){
        $integrado 	    = $this->integCurrent->integrados[0];

        $number2string  = new AifLibNumber();

        $integ = new IntegradoSimple($integrado->integrado->integradoId);


        $html = '
                    <div class="clearfix" id="logo">
                        <div class="span6"><img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" /></div>
                        <h3 class="span2 text-right">No. Orden</h3><h3 class="span2 bordes-box text-center">'.$this->odd->numOrden.'</h3>
                    </div>	
                    
                    <h1>'.JText::_('LBL_ORDEN_DE_DEPOSITO').'</h1>
                    
                    <table class="clearfix" id="cabecera">
                        <tr>
                            <td class="span2 text-right">
                                '.JText::_('LBL_SOCIO_INTEG').'
                            </td>
                            <td class="span4">
                                '.$this->odd->receptor.'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_DATE_CREATED').'
                            </td>
                            <td class="span4">
                                '.$this->odd->createdDate.'
                            </td>
                        </tr>
                        <tr>
                            <td class="span2 text-right">
                                '.JText::_('LBL_MONEDA').'
                            </td>
                            <td class="span4">
                                ';
                            $this->odd->currency = isset($this->odd->currency)? $this->odd->currency:'MXN';

                    $html .= $this->odd->currency.'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_PAYMENT_DATE').'
                            </td>
                            <td class="span4">';

                        if (isset($this->odd->paymentDate)) {$html .=$this->odd->paymentDate;}

                    $html .='</td>
                        </tr>
                        <tr>
                            <td class="span2 text-right">
                                '.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'
                            </td>
                            <td class="span4">
                                '.$integ->getDisplayName().'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_FORMA_PAGO').'
                            </td>
                            <td class="span4">
                                '.JText::_($this->odd->paymentMethod->name).'
                            </td>
                        </tr>
                        <tr>
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
                        <tr>
                            <td class="span2 text-right">
                                '.JText::_('LBL_CORREO').'
                            </td>
                            <td class="span4">
                                '.$integrado->datos_personales->email.'
                            </td>
                            <td class="span2 text-right">
                                '.JText::_('LBL_BANCO_CUENTA').'
                            </td>
                            <td class="span4">';
                                if (isset($integrado->datos_bancarios[0]->banco_cuenta)) { $html .= 'XXXXXX' . substr($integrado->datos_bancarios[0]->banco_cuenta, -4, 4); }
                    $html .='
                            </td>
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
                                <tr>
                                    <td style="border: 1px solid #ddd;">$ '.number_format($this->odd->totalAmount,2). ' ' . $this->odd->currency.'</td>
                                    <td style="border: 1px solid #ddd;">'.$number2string->toCurrency('$ '.number_format($this->odd->totalAmount,2)).'</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="control-group" id="tabla-bottom">
                            <tr>
                                <td>
                                    '.JText::_('LBL_OBSERVACIONES').'
                                </td>
                            </tr>
                        </table>
                        <table id="footer">
                            <tr class="container">
                                <td class="control-group">
                                    '.JText::_('LBL_DATOS_DEPOSITO').'
                                </td>
                                <td class="container text-uppercase control-group">
                                    '.JText::_('LBL_AUTORIZO_ODD').'
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td>
                                    <p class="text-capitalize">'.JText::_('LBL_INTEGRADORA').'</p>
                                    <p>'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                                </td>
                            </tr>
                        </table>
                ';
        return $html;
    }
}