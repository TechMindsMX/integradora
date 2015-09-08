<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 08/09/2015
 * Time: 11:02 AM
 */

class cashinPDF{
    
    public  function generateHtml($data){

        $html ='<body>
                <table style="width: 100%" id="logo">
                <tr style="font-size: 10px">
                    <td style="width: 569px;">
                        <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </td>
                </tr>
            </table>';
        $html .= '
                    <h1>'.JText::_('LBL_CASH_OUT').'</h1>';

        $html .='
                        <h3>'.JText::_('LBL_DESCRIP_CASHOUT').'</h3>
                        <table style="border: 1px solid #ddd;">
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_USER_UUID').'</th>
                                    <td style="border: 1px solid #ddd;">'.$data->integradoId.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_CLABE').'</th>
                                    <td style="border: 1px solid #ddd;">'.$data.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_BANCK_CODE').'</th>
                                    <td style="border: 1px solid #ddd;">'.$data->objEnvio->bankCode.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_AMOUNT').'</th>
                                    <td style="border: 1px solid #ddd;">$ '.number_format($data->objEnvio->amount,2). ' ' . $data->currency.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_TIMESTAMP').'</th>
                                    <td style="border: 1px solid #ddd;">'.date('d-m-Y').'</td>
                                </tr>
                        </table>
                        <table id="footer">
                            <tr class="container">
                                <td class="control-group" style="font-size: 10px">
                                     <br /><br />
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