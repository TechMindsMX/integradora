<?php
defined('JPATH_PLATFORM') or die;

define('JPATH_BASE', realpath(dirname(__FILE__).'/../..'));
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
jimport('integradora.integralib.order');
jimport('integradora.integrado');
jimport('integradora.gettimone');
require('html2pdf.class.php');
require('_class/Facpdf.php');
require('_class/odcPdf.php');
require('_class/oddPdf.php');
require('_class/odrPdf.php');

class reportecontabilidad{


    function __construct($integ_id = null) {

        $integradora = new \Integralib\Integrado();
        $this->integradora = new IntegradoSimple($integradora->getIntegradoraUuid() );

        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');

        $integrado = new IntegradoSimple($this->integradoId);

        $integrado->getTimOneData();

        $this->integCurrent = $integrado;
    }

    public function facturaPDF($data, $facObjOdv, $facObj, $xml){

        $fileName = explode('/', $xml);
        $fileName = explode('.', $fileName[2]);

        $path = JPATH_BASE.'/media/facturas/'.$fileName[0].'.pdf';


        $createHtml = new Facpdf();
        $html = $createHtml->html($data, $this->integradora, $facObjOdv, $facObj);

        $html2pdf = new HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output($path, 'F');
        return $path;
    }


    public function createPDF($data, $tipo)
    {
        $path = '';
        switch ($tipo){
            case 'odv':
                $html = $this->odv($data);
                break;
            case 'odc':
                $getHtml = new odcPdf();

                $orden = getFromTimOne::getOrdenesCompra(null, $data);
                $orden = $orden[0];

                $html = $getHtml->html($orden);
                $path = 'media/pdf_odc/'.$tipo.'-'.$data.'.pdf';
                break;
            case 'odd':

                $getHtml = new oddPdf($data);

                $html = $getHtml->createHTML();
                $path = 'media/pdf_odd/'.$tipo.'-'.$data[0]->numOrden.'.pdf';
                break;
            case 'odr':
                $getHtml = new odrPdf($data);

                $html = $getHtml->createHTML();
                $path = 'media/pdf_odr/'.$tipo.'-'.$data->numOrden.'.pdf';
                break;
            default:
                $operacion='';
        }

        $css = $this->readCss();
        $html = '<style>
                body{
                    color: #777;
                    font-size: 13px;
                    font-weight: normal;
                    line-height: 24.05px;
                }
                table{
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

                </style>'.$html;

        $html2pdf = new HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output($path, 'F');
    }

    public  function readCss(){
        return $this->readFile('http://localhost/integradora/templates/meet_gavern/bootstrap/output/bootstrap.css');
    }

    function odv($data){
        $this->readCss();

        $document	= JFactory::getDocument();
        $app 		= JFactory::getApplication();
        $template = $app->getTemplate();

        // Datos
        $params 	= array (
                'option' => 'com_reportes',
                'view' => 'reportescontabilidad',
                'idOrden' => $data->numOrden,
                'tmpl' => 'component',
                'print' => '1',
                'Itemid' => NULL,
            );

        $number2word = new AifLibNumber();
        $document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
        $html = "<style>
        ".$this->css."
        </style>
        <table class=\"table\">
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
            <table class="table" id="data" style="font-size: 10px">
                <tr style="font-size: 10px" >
                    <td colspan="4"><h4>'.JText::_('LBL_ORDEN_DE_VENTA').'</h4></td>
                </tr>
                <tr >
                    <td style="text-align: right; width: 17%;">'.JText::_('LBL_SOCIO_INTEG').'</td>
                    <td style="text-align: left;">'.$data->emisor->integrados[0]->datos_empresa->razon_social.'</td>
                    <td style="text-align: right;">'.JText::_('LBL_DATE_CREATED').'</td>
                    <td style="text-align: left; width: 20%;">'.$data->createdDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_PROY').'</td>

                    <td style="text-align: left;">';
                            isset( $data->proyecto->name ) ? $html .= $data->proyecto->name : $html .='';

                   $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_PAYMENT_DATE').'</td>
                    <td style="text-align: left;">'.$data->paymentDate.'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_SUBPROY').'</td>
                    <td style="text-align: left;">';
                            isset($data->subproyecto->name) ? $html .=$data->subproyecto->name : $html .='';

                   $html .= '</td>
                    <td style="text-align: right;">'.JText::_('LBL_FORMA_PAGO').'</td>
                    <td style="text-align: left;">'.JText::_($data->paymentMethod->name).'</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_MONEDA').'</td>
                    <td style="text-align: left;">';

                    isset($data->currency) ? $html .=$data->currency : $html .='MXN';

        $html .='</td>
                    <td style="text-align: right;">'.JText::_('LBL_BANCO_CUENTA').'</td>
                    <td style="text-align: left;">';
                        isset($data->account[0]->banco_cuenta) ? $html .='XXXXXX' . substr($data->account[0]->banco_cuenta, -4, 4) : $html .= 'Sin Identificar';

        $receptor = $data->receptor->integrados[0]->datos_empresa;
        $html .='</td>
                </tr>
                <tr>
                    <td colspan="4"><h5>'.JText::_('LBL_HEADER_DATOS_CLIENTE').'</h5></td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RAZON_SOCIAL').'</td>
                    <td style="text-align: left;">'.$receptor->razon_social.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_RFC').'</td>
                    <td style="text-align: left;">'.$receptor->rfc.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_CONTACT').'</td>
                    <td style="text-align: left;">'.$receptor->razon_social.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('COM_MANDATOS_CLIENTES_PHONE').'</td>
                    <td style="text-align: left;">'.$receptor->tel_fijo.'</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">'.JText::_('LBL_CORREO').'</td>
                    <td style="text-align: left;">';
            isset($data->receptor->integrados[0]->datos_personales->email) ? $html .= $data->receptor->integrados[0]->datos_personales->email : $html .=$data->receptor->user->email;
        $html  .='</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: left;">&nbsp;</td>
                </tr>';

       $html .= '</table>';

        $html .= '<div class="clearfix"><h6>'.JText::_('LBL_DESCRIP_PRODUCTOS').'</h6></div>
                    <table style="border: 1px solid #ddd; width: 500px !important; font-size= 10px" class="table table-bordered">
                        <thead style="border: 1px solid #ddd">
                        <tr style="border: 1px solid #ddd">
                            <th >#</th>
                            <th >'.JText::_('LBL_CANTIDAD').'</th>
                            <th >'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION').'</th>
                            <th >'.JText::_('LBL_UNIDAD').'</th>
                            <th >'.JText::_('LBL_P_UNITARIO').'</th>
                            <th >'.JText::_('LBL_IMPORTE').'</th>
                        </tr>
                        </thead>
                        <tbody style="border: 1px solid #ddd">';

                        foreach ($data->productosData as $key => $prod){
                            $html .='<tr>
                                <td>';  $html .=$key+1;
                            $html .='</td><td>';
                                if ( ! empty( $prod->cantidad ) ) {
                                    $html .=$prod->cantidad;
                                }

                            $html .='</td>
                                <td>';
                                if ( ! empty( $prod->descripcion ) ) {
                                    $html .= '<strong>'. $prod->producto .'</strong><br />'. $prod->descripcion;
                                }
                            $html .='</td>
                                <td>';
                                if ( ! empty( $prod ) ) {
                                    $html .= $prod->unidad;
                                }
                            $html .='</td>
                                <td><span>$';

                                if ( ! empty( $prod->p_unitario ) ) {
                                    $html .=number_format($prod->p_unitario,2);
                                }
                            $html .='</span></td>
                                <td><span >$';

                                if ( ! empty( $prod->cantidad ) ) {
                                    $valor = number_format(floatval($prod->cantidad) * floatval($prod->p_unitario),2);
                                    $html .=$valor;
                                }
                            $html .='</span></td>
                            </tr>';
                        }

                        $html .='<tr>
                            <td colspan="4">
                                '.JText::_('LBL_MONTO_LETRAS').' <span>'.$number2word->toCurrency('$'.number_format($data->totalAmount, 2)).'</span>
                            </td>
                            <td class="span2">
                                '.JText::_('LBL_SUBTOTAL').'
                            </td>
                            <td><div class="text-right">
                                    $';
                        $html .=number_format($data->subTotalAmount,2);

                        $html .='
                                </div></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td class="span2">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA').'
                            </td>

                            <td><div class="text-right">
                                    $';
                        $html .= number_format($data->iva, 2);

                        $html .='
                                </div></td>
                        </tr>
                        <tr>
                        <td colspan="4"></td>
                            <td class="span2">
                                '.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS').'
                            </td>
                            <td><div class="text-right">
                                    $';
                        $html .=number_format($data->ieps, 2);

                        $html .='
                                </div></td>
                        </tr>
                        <tr>
                        <td colspan="4"></td>
                            <td class="span2">
                                '.JText::_('LBL_TOTAL').'
                            </td>
                            <td><div>
                                    $';
                        $html .=number_format($data->totalAmount, 2);

                        $html .='
                                </div></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table" id="printFooter">
                        <tr>
                            <td>'.JText::_('LBL_CON_FACTURA').'</td>
                        </tr>
                        <tr>
                            <td>'.JText::_('LBL_AUTORIZO_FACTURA').'</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <p class="text-capitalize">'.JText::_('LBL_INTEGRADORA').'</p>
                                <p>'.JText::_('LBL_INTEGRADORA_DIRECCION').'</p>
                            </td>
                        </tr>
                    </table>';
        return $html;
    }

    public  function readFile ($url){
        $this->css = '';
        $file = fopen($url, "r") or exit("Unable to open file!");
        while(!feof($file)) {
            $this->css .= fgets($file);
            $this->css = str_replace("inherit", "", $this->css);
        }
        return $this->css;
        fclose($file);
    }
}