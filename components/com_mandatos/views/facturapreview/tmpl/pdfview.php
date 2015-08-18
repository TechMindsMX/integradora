<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
require('libraries/html2pdf/html2pdf.class.php');
require('libraries/html2pdf/reportecontabilidad.php');

JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params      = $app->input->getArray();
$integrado   = $this->integCurrent->integrados[0];
$xml         = $this->factura->datosXML;
$integradora = new \Integralib\Integrado();
$integradora = new IntegradoSimple( $integradora->getIntegradoraUuid() );
$number2word = new AifLibNumber();
$document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
$fechaHOra = explode('T',$xml->comprobante['FECHA']);
$fecha = explode('-',$fechaHOra[0]);
$fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
$hora = $fechaHOra[1];

$css = new reportecontabilidad();

$style = $css->readCss();

$html =$style.'
<table class="table">
    <tr>
        <td>
            <div><img width="200" src="'.JUri::base().'images/logo_iecce.png"/></div>
        </td>
    </tr>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4>'.JText::_('LBL_FACTURA_DE_VENTA').'</h4></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 35px;" colspan="2"><h4>'.$integradora->getDisplayName().'</h4></td>
    </tr>
    <tr>
        <td style="text-align: left;  padding-left: 35px;">'.$integradora->integrados[0]->datos_empresa->rfc.'</td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;">Folio: </td>
        <td style="text-align: left; width: 20%;">'.$xml->comprobante['FOLIO'].'</td>
    </tr>
    <tr>
        <td style="text-align: left;  padding-left: 35px; width: 500px;">'.$integradora->integrados[0]->address.'</td>
        <td></td>
        <td style="text-align: right;">Elaboraci&oacute;n</td>
        <td style="text-align: left;">'.$fecha.'</td>
    </tr>
    <tr>
        <td style="text-align: left;padding-left: 35px;" colspan="2">'.JText::_('LBL_MONEDA').': '.$this->factura->currency.'</td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">'.$hora.'</td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 36px; padding-top: 15px;" colspan="4">
            <p>'.$this->factura->getReceptor()->getIntegradoRfc().' - '.$this->factura->proveedor->frontName.'</p>
            <p>
                '.$xml->receptor['children'][0]['attrs']['CALLE'].'
            </p>
        </td>
    </tr>
</table>';

$html .= '<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span1">#</th>
        <th class="span1">'.JText::_('LBL_CANTIDAD').'</th>
        <th class="span4">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION').'</th>
        <th class="span2">'.JText::_('LBL_UNIDAD').'</th>
        <th class="span2">'.JText::_('LBL_P_UNITARIO').'</th>
        <th class="span2">'.JText::_('LBL_IMPORTE').'</th>
    </tr>
    </thead>
    <tbody>';

    foreach ($this->factura->productosData as $key => $prod) :
        $ivasProd = array();
        array_push($ivasProd, floatval($prod->iva) );

        $html .='<tr>
            <td>';
            $html .= $key+1;
        $html .='</td>
            <td>'.$prod->cantidad.'</td>
            <td>'.$prod->descripcion.'</td>
            <td>'.$prod->unidad.'</td>
            <td>
                <div class="text-right">
                    '.number_format($prod->p_unitario, 2).'
                </div>
            </td>
            <td>
                <div class="text-right">
                    '.number_format(floatval($prod->cantidad) * floatval($prod->p_unitario), 2).'
                </div>
            </td>
        </tr>';

    endforeach;
    $html .='
    <tr>
        <td colspan="4" rowspan="3">
            '.JText::_('LBL_MONTO_LETRAS').'
            <span>'.$number2word->toCurrency('$' . number_format($this->factura->getTotalAmount(), 2)).'</span>
        </td>
        <td class="span2">
            '.JText::_('LBL_SUBTOTAL').'
        </td>
        <td>
            <div class="text-right">
                '.number_format($this->factura->subTotalAmount, 2).'
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            '.array_sum($ivasProd)/count($ivasProd) . '% ' . JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
        </td>
        <td>
            <div class="text-right">
                '.number_format($this->factura->iva, 2).'
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            '.JText::_('LBL_TOTAL').'
        </td>
        <td>
            <div class="text-right">
                '.number_format($this->factura->getTotalAmount(), 2).'
            </div>
        </td>
    </tr>
    </tbody>
</table>
';

$html .='<table class="table" id="printFooter">
    <tr>
        <td colspan="4">
            '.JText::_('LBL_AUTORIZO_ODV').$this->integCurrent->getDisplayName().' con RFC: '.$this->integCurrent->integrados[0]->datos_empresa->rfc.'
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border-top: 1px solid rgba(0, 0, 0, 0.21); margin-top: 2px;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Folio fiscal:</strong> </td>
        <td style="text-align: left;">'.$xml->complemento['children'][0]['attrs']['UUID'].'</td>
        <td style="text-align: right;" colspan="2" rowspan="5">
            <img src="media/qrcodes/'.$this->factura->createdDate.'-'.$this->factura->integradoId.'-'.$this->factura->id.'.png">
    </td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>No. serie de CSD del emisor:</strong> </td>
        <td style="text-align: left;">'.$xml->comprobante['NOCERTIFICADO'].'</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Fecha y hora de certificaci&oacute;n</strong>:</td>
        <td style="text-align: left;">'.$fecha.' '.$hora.'</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Fecha y hora de emisi&oacute;n:</strong> </td>
        <td style="text-align: left;">'.$fecha.' '.$hora.'</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>No. serie del CSD del SAT:</strong> </td>
        <td style="text-align: left;">'.$xml->complemento['children'][0]['attrs']['NOCERTIFICADOSAT'].'</td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Sello Digital del CFDI:</strong></span></p>
            <p style="font-size: 5px;">'.$xml->complemento['children'][0]['attrs']['SELLOCFD'].'</p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Cadena Original del COmplemento de Certificaci&oacute;n digital del SAT:</strong></span></p>
            <p style="font-size: 5px;">'.chunk_split($xml->comprobante['CERTIFICADO'], 200).'</p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Sello del SAT:</strong></span></p>
            <p style="font-size: 5px;">'.$xml->complemento['children'][0]['attrs']['SELLOSAT'].'</p>
        </td>
    </tr>
</table>
';
echo $html;
?>



<?php

$html2pdf = new HTML2PDF();
$html2pdf->WriteHTML($html);
$html2pdf->Output('respaldosPDF/Factura/Factura-Num-'.$data->numOrden.'.pdf', 'F');
?>