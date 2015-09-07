<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 07/09/2015
 * Time: 12:37 PM
 */

class cashoutPDF{

    public function __construct($data){
        $this->tx = $data;
        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integCurrent = new IntegradoSimple($this->integradoId);
    }

    public  function generateHtml(){

        $html ='<body>
                <table style="width: 100%" id="logo">
                <tr style="font-size: 10px">
                    <td style="width: 569px;">
                        <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </td>
                    <td >
                        <h3 class=" bordes-box text-center">'.$this->tx->numOrden.'</h3>
                    </td>
                </tr>
            </table>';
        $html .= '
                    <h1>'.JText::_('LBL_CASH_OUT').'</h1>';

        $html .='
                        <h3>'.JText::_('LBL_DESCRIP_CASHOUT').'</h3>
                        <table style="border: 1px solid #ddd;">
                            <thead>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px" ></th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_USER_UUID').'</th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_USER_UUID').'</th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_CLABE').'</th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_BANCK_CODE').'</th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_AMOUNT').'</th>
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_TIMESTAMP').'</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-size: 10px">
                                    <td style="border: 1px solid #ddd;">$ '.number_format($this->tx->objEnvio->uuid,2). ' ' . $this->tx->currency.'</td>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->clabe.'</td>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->bankCode.'</td>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->amount.'</td>
                                    <td style="border: 1px solid #ddd;">'.date('d-m-Y').'</td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="footer">
                            <tr class="container">
                                <td class="control-group" style="font-size: 10px">
                                    '.JText::_('LBL_DATOS_DEPOSITO').'
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px; line-height: 24px; font-size: 10px" class="container text-uppercase control-group">
                                    '.JText::_('LBL_AUTORIZO_tx').'
                                </td>
                            </tr>
                            <tr >
                                <td style="line-height: 24px; font-size: 10px">
                                    <p class="text-capitalize" style="text-align: center; ">'.JText::_('LBL_INTEGRADORA').'</p>
                                    <p style="text-align: center">'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                                </td>
                            </tr>
                        </table>
                        </body>
                ';
        return $html;
    }
}