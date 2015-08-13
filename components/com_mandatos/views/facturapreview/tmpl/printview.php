<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

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

?>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>
<table class="table">
    <tr>
        <td>
            <div><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
        </td>
        <td style="text-align: right">
            <h3 class="text-right">No. <?php echo $this->factura->getId(); ?></h3>
        </td>
    </tr>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 35px;" colspan="4"><h4><?php echo $integradora->getDisplayName(); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo $integradora->integrados[0]->datos_empresa->rfc ?></td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;">Folio: </td>
        <td style="text-align: left; width: 20%;"><?php echo $xml->comprobante['FOLIO']; ?></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 35px;" colspan="2"><?php echo $integradora->integrados[0]->address; ?></td>
        <td style="text-align: right;">Elaboraci&oacute;n</td>
        <td style="text-align: left;"><?php echo $fecha ?></td>
    </tr>
    <tr>
        <td style="text-align: left;padding-left: 35px;" colspan="2"><?php echo JText::_('LBL_MONEDA').': '.$this->factura->currency; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;"><?php echo $hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 36px; padding-top: 15px;" colspan="4">
            <p><?php echo $this->factura->getReceptor()->getIntegradoRfc().' - '.$this->factura->proveedor->frontName; ?></p>
            <p>
                <?php echo $xml->receptor['children'][0]['attrs']['CALLE']; ?>
            </p>
        </td>
    </tr>
</table>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span1">#</th>
        <th class="span1"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
        <th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_UNIDAD'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->factura->productosData as $key => $prod) :
        $ivasProd = array();
        array_push($ivasProd, floatval($prod->iva) );
        ?>
        <tr>
            <td><?php echo $key+1; ?></td>
            <td><?php echo $prod->cantidad; ?></td>
            <td><?php echo $prod->descripcion; ?></td>
            <td><?php echo $prod->unidad; ?></td>
            <td>
                <div class="text-right">
                    <?php echo number_format($prod->p_unitario, 2); ?>
                </div>
            </td>
            <td>
                <div class="text-right">
                    <?php echo number_format(floatval($prod->cantidad) * floatval($prod->p_unitario), 2); ?>
                </div>
            </td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td colspan="4" rowspan="3">
            <?php echo JText::_('LBL_MONTO_LETRAS'); ?>
            <span><?php echo $number2word->toCurrency('$' . number_format($this->factura->getTotalAmount(), 2)); ?></span>
        </td>
        <td class="span2">
            <?php echo JText::_('LBL_SUBTOTAL'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->subTotalAmount, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo array_sum($ivasProd)/count($ivasProd) . '% ' . JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->iva, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo JText::_('LBL_TOTAL'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->getTotalAmount(), 2); ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<table class="table" id="printFooter">
    <tr>
        <td colspan="4">
            <?php echo JText::_('LBL_AUTORIZO_ODV').$this->integCurrent->getDisplayName().' con RFC: '.$this->integCurrent->integrados[0]->datos_empresa->rfc; ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border-top: 1px solid rgba(0, 0, 0, 0.21); margin-top: 2px;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Folio fiscal:</strong> </td>
        <td style="text-align: left;"><?php echo $xml->complemento['children'][0]['attrs']['UUID']; ?></td>
        <td style="text-align: right;" colspan="2" rowspan="5"></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>No. serie de CSD del emisor:</strong> </td>
        <td style="text-align: left;"><?php echo $xml->comprobante['NOCERTIFICADO']; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Fecha y hora de certificaci&oacute;n</strong>:</td>
        <td style="text-align: left;"><?php echo $fecha.' '.$hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>Fecha y hora de emisi&oacute;n:</strong> </td>
        <td style="text-align: left;"><?php echo $fecha.' '.$hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong>No. serie del CSD del SAT:</strong> </td>
        <td style="text-align: left;"><?php echo $xml->complemento['children'][0]['attrs']['NOCERTIFICADOSAT']; ?></td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Sello Digital del CFDI:</strong></span></p>
            <p style="font-size: 5px;"><?php echo $xml->complemento['children'][0]['attrs']['SELLOCFD']; ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Cadena Original del COmplemento de Certificaci&oacute;n digital del SAT:</strong></span></p>
            <p style="font-size: 5px;"><?php echo chunk_split($xml->comprobante['CERTIFICADO'], 200); ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong>Sello del SAT:</strong></span></p>
            <p style="font-size: 5px;"><?php echo $xml->complemento['children'][0]['attrs']['SELLOSAT']; ?></p>
        </td>
    </tr>
</table>
