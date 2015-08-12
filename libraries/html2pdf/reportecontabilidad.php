<?php
defined('JPATH_PLATFORM') or die;
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
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


        $html2pdf = new HTML2PDF('P','A4','fr');
        $html2pdf->WriteHTML($html);
        $html2pdf->Output('respaldosPDF/exemple.pdf', 'F');
        exit;
    }

    function odv($data){
        $document	= JFactory::getDocument();
        $app 		= JFactory::getApplication();
        $template = $app->getTemplate();

        // Datos
        $params 	= array (
                'option' => 'com_mandatos',
                'view' => 'odvpreview',
                'layout' => 'printview',
                'idOrden' => '3',
                'tmpl' => 'component',
                'print' => '1',
                'Itemid' => NULL,
            );


        $number2word = new AifLibNumber();
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );

        $html = "<table class=\"table\">
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
                    <td style="text-align: left;">PONER EMISOR</td>
                    <td style="text-align: right;">'.JText::_('LBL_DATE_CREATED').'</td>
                    <td style="text-align: left; width: 20%;">DATA CREATED</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_PROY').'</td>

                    <td style="text-align: left;">';
                            isset( $data->proyecto->name ) ? $data->proyecto->name : '';


                   $html .= '</td>
                    <td style="text-align: right;">LBL_PAYMENT_DATE</td>
                    <td style="text-align: left;">'.$data->paymentDate.'</td>
                </tr>';
                /*
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_SUBPROY').'</td>
                    <td style="text-align: left;">'.isset($data->subproyecto->name) ? $data->subproyecto->name : ''.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_FORMA_PAGO').'</td>
                    <td style="text-align: left;">'.JText::_($data->paymentMethod->name).'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_MONEDA').'</td>
                    <td style="text-align: left;">'.isset($data->currency) ? $data->currency : 'MXN'.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_BANCO_CUENTA').'</td>
                    <td style="text-align: left;">'.isset($data->account[0]->banco_cuenta) ? 'XXXXXX' . substr($data->account[0]->banco_cuenta, -4, 4) : ''.'</td>
                </tr>
                <tr>
                    <td colspan="5"><h5>'.JText::_('LBL_HEADER_DATOS_CLIENTE').'</h5></td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RAZON_SOCIAL').'</td>
                    <td style="text-align: left;">'.$data->getReceptor()->getDisplayName().'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RFC').'</td>
                    <td style="text-align: left;">'.$data->getReceptor()->getIntegradoRfc().'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'</td>
                    <td style="text-align: left;">'.$data->getReceptor()->getContactName().'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_PHONE').'</td>
                    <td style="text-align: left;">'.$data->getReceptor()->getIntegradoPhone().'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_CORREO').'</td>
                    <td style="text-align: left;">'.$data->getReceptor()->getIntegradoEmail().'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>        */

       $html .= '</table>';

        return $html;
    }
}