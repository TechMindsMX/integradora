<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 07/09/2015
 * Time: 12:37 PM
 */

class cashoutPDF{

    public function __construct($data){
        $this->tx = new stdClass();

//        $propGetter = Closure::bind( function($prop){return $this->$prop;}, $data, $data );

        $this->tx->objEnvio = $data->getObjEnvio();
        $this->tx->result = $data->getResultado();

        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integCurrent = new IntegradoSimple($this->integradoId);
    }

    public  function generateHtml(){

        $html ='<body>
                <table style="width: 100%" id="logo">
                <tr style="font-size: 10px">
                    <td style="width: 569px;">
                        <img width="200" src="images/logo_iecce.png" />
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
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_USER_UUID').'</th>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->uuid.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_CLABE').'</th>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->clabe.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_BANCK_CODE').'</th>
                                    <td style="border: 1px solid #ddd;">'.$this->tx->objEnvio->bankCode.'</td>
                                </tr>
                                <tr style="border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; width: 350px">'.JText::_('LBL_AMOUNT').'</th>
                                    <td style="border: 1px solid #ddd;">$ '.number_format($this->tx->objEnvio->amount,2). ' ' . $this->tx->currency.'</td>
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